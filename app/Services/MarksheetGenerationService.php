<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\GeneratedMarksheet;
use App\Models\MarksheetTemplate;
use App\Models\Result;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class MarksheetGenerationService
{
    public function __construct(
        private readonly ResultCalculationService $resultService
    ) {}

    /**
     * Generate marksheet for a single student.
     * Returns the generated file path.
     */
    public function generateForStudent(Student $student, Exam $exam, MarksheetTemplate $template): string
    {
        // Ensure result is calculated
        $result = $this->resultService->calculateForStudent($student, $exam);

        $school = School::first();
        $placeholders = $this->buildPlaceholders($student, $exam, $result, $school, $template);

        // Load template
        $templatePath = Storage::disk('local')->path($template->file_path);
        $processor    = new TemplateProcessor($templatePath);

        // Handle Images (Logo and Signature) BEFORE text variables
        // Hack: flatten tags before inserting image to fix fragmented XML macros and ensure VML is correctly placed
        try { $processor->setValue('principal_signature', '${principal_signature}', 1); } catch (\Exception $e) {}
        try { $processor->setValue('school_logo', '${school_logo}', 1); } catch (\Exception $e) {}

        // Handle Images (Logo and Signature) using the $school already loaded above
        if ($school && $school->signature) {
            $sigPath = Storage::disk('public')->path($school->signature);
            \Illuminate\Support\Facades\Log::info("Trying to insert signature from: " . $sigPath);
            if (file_exists($sigPath)) {
                try {
                    $processor->setImageValue('principal_signature', ['path' => $sigPath, 'width' => '120pt', 'height' => '40pt', 'ratio' => false]);
                    \Illuminate\Support\Facades\Log::info("Signature inserted successfully.");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Signature insert failed: " . $e->getMessage());
                }
            } else {
                \Illuminate\Support\Facades\Log::error("Signature file not found at: " . $sigPath);
            }
        }

        if ($school && $school->logo) {
            $logoPath = Storage::disk('public')->path($school->logo);
            if (file_exists($logoPath)) {
                try {
                    $processor->setImageValue('school_logo', ['path' => $logoPath, 'width' => '80pt', 'height' => '80pt', 'ratio' => false]);
                } catch (\Exception $e) {}
            }
        }

        // Wipe out image placeholders if they weren't matched and replaced above
        try { $processor->setValue('principal_signature', ''); } catch (\Exception) {}
        try { $processor->setValue('school_logo', ''); } catch (\Exception) {}

        // Fill all mapped placeholders
        $mappings = $template->field_mappings ?? [];
        foreach ($mappings as $placeholder => $fieldKey) {
            $value = $placeholders[$fieldKey] ?? '';
            $this->replaceBulletproof($processor, $placeholder, $value);
        }

        // Also try direct placeholder name match
        foreach ($placeholders as $key => $value) {
            $this->replaceBulletproof($processor, $key, $value);
        }

        // Handle subject table rows if template has {{subject_row}} block
        $this->fillSubjectTable($processor, $result);

        // Save
        $filename  = "marksheets/{$exam->id}/{$student->id}_marksheet.docx";
        $outputPath = Storage::disk('local')->path($filename);

        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0775, true);
        }

        $processor->saveAs($outputPath);

        // Record in DB
        GeneratedMarksheet::updateOrCreate(
            ['student_id' => $student->id, 'exam_id' => $exam->id, 'template_id' => $template->id, 'user_id' => $student->user_id],
            ['file_path' => $filename, 'file_type' => 'docx', 'generated_at' => now()]
        );

        return $filename;
    }

    /**
     * Generate marksheets for all students in a class-section and return ZIP path.
     */
    public function generateForClass(
        int $classId, int $sectionId, Exam $exam, MarksheetTemplate $template
    ): string {
        $students  = Student::where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->orderBy('roll')
            ->get();

        $files = [];
        foreach ($students as $student) {
            $files[] = Storage::disk('local')->path(
                $this->generateForStudent($student, $exam, $template)
            );
        }

        // Bundle into ZIP
        $zipFilename = "marksheets/{$exam->id}/class_{$classId}_section_{$sectionId}.zip";
        $zipPath     = Storage::disk('local')->path($zipFilename);

        if (!is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0775, true);
        }

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($files as $file) {
            if (file_exists($file)) {
                $zip->addFile($file, basename($file));
            }
        }
        $zip->close();

        return $zipFilename;
    }

    /**
     * Extract placeholder tags (e.g., {{student_name}}) from a .docx file.
     */
    public function extractPlaceholders(string $filePath): array
    {
        $fullPath = Storage::disk('local')->path($filePath);
        $processor = new TemplateProcessor($fullPath);

        // PHPWord exposes getVariables() on TemplateProcessor, but they might be polluted with extra brackets if the user dragged/dropped poorly.
        $vars = $processor->getVariables();
        $cleaned = [];
        foreach ($vars as $var) {
            $cleaned[] = str_replace(['{', '}', '$'], '', $var);
        }
        return array_values(array_unique($cleaned));
    }

    private function replaceBulletproof(TemplateProcessor $processor, string $key, string $value): void
    {
        $safeValue = htmlspecialchars((string) $value);
        $cleanKey = str_replace(['{', '}', '$'], '', $key);
        
        // Pass exact strings that start with ${ so PHPWord doesn't alter them.
        // Try largest to smallest to consume all surrounding brackets!
        $processor->setValue('${${' . $cleanKey . '}}}', $safeValue);
        $processor->setValue('${${' . $cleanKey . '}}', $safeValue);
        $processor->setValue('${{' . $cleanKey . '}}}', $safeValue);
        $processor->setValue('${{' . $cleanKey . '}}', $safeValue);
        $processor->setValue('${' . $cleanKey . '}}}', $safeValue);
        $processor->setValue('${' . $cleanKey . '}}', $safeValue);
        $processor->setValue('${' . $cleanKey . '}', $safeValue);
    }

    // ----------------------------------------------------------------

    private function buildPlaceholders(
        Student $student, Exam $exam, Result $result, ?School $school, MarksheetTemplate $template
    ): array {
        $p = [];

        // Student
        $p['student_name']      = $student->name;
        $p['gender']            = $student->gender ?? '';
        $p['roll']              = $student->roll;
        $p['registration_no']   = $student->registration_no ?? '';
        $p['father_name']       = $student->father_name ?? '';
        $p['mother_name']       = $student->mother_name ?? '';
        $p['class_name']        = $student->schoolClass->name ?? '';
        $p['section_name']      = $student->section->name ?? '';
        $p['session']           = $student->session ?? '';

        // Exam
        $p['exam_name']  = $exam->name;
        $p['exam_year']  = $exam->year;

        // Result
        $p['total_marks']  = $result->total_marks;
        $p['full_marks']   = $result->full_marks;
        $p['percentage']   = number_format($result->percentage, 2) . '%';
        $p['gpa']          = number_format($result->gpa, 2);
        $p['grade']        = $result->grade;
        $p['division']     = $result->division;
        $p['result_status']= $result->is_passed ? 'PASSED' : 'FAILED';
        $p['rank']         = $result->rank ?? 'N/A';

        foreach ($result->subject_details ?? [] as $detail) {
            $key = trim(strtolower($detail['subject_code'] ?? ''));
            if (!$key) {
                $key = strtolower(str_replace(' ', '_', $detail['subject_name']));
            }
            $p["{$key}_obtained"] = $detail['obtained'];
            $p["{$key}_full"]     = $detail['full'];
            $p["{$key}_grade"]    = $detail['grade'];
            $p["{$key}_gpa"]      = $detail['gpa'];

            if (!empty($detail['has_sub_subjects'])) {
                foreach ($detail['sub_subjects'] ?? [] as $subDetail) {
                    $subKey = strtolower(str_replace(' ', '_', $subDetail['name']));
                    $p["{$key}_{$subKey}_obtained"] = $subDetail['obtained'];
                    $p["{$key}_{$subKey}_full"]     = $subDetail['full'];
                    $p["{$key}_{$subKey}_grade"]    = $subDetail['grade'];
                    $p["{$key}_{$subKey}_gpa"]      = $subDetail['gpa'];

                    foreach ($subDetail['components'] ?? [] as $comp => $cd) {
                        $safeComp = strtolower(str_replace(' ', '_', $comp));
                        $p["{$key}_{$subKey}_{$safeComp}"] = $cd['obtained'];
                    }
                }
            } else {
                foreach ($detail['components'] ?? [] as $comp => $cd) {
                    $safeComp = strtolower(str_replace(' ', '_', $comp));
                    $p["{$key}_{$safeComp}"] = $cd['obtained'];
                }
            }
        }

        // School
        if ($school) {
            $p['school_name']    = $school->name;
            $p['school_address'] = $school->address ?? '';
            $p['school_phone']   = $school->phone ?? '';
            $p['school_email']   = $school->email ?? '';
            $p['footer_text']    = $school->footer_text ?? '';
        }

        $p['generated_date'] = now()->format('d/m/Y');

        return $p;
    }

    private function fillSubjectTable(TemplateProcessor $processor, Result $result): void
    {
        try {
            $details  = $result->subject_details ?? [];
            $rows     = [];
            $sno      = 1;

            foreach ($details as $d) {
                if (!empty($d['has_sub_subjects'])) {
                    $componentsStrings = [];
                    foreach ($d['sub_subjects'] ?? [] as $sub) {
                        $subStr = collect($sub['components'])
                            ->map(fn($v, $k) => strtoupper($k) . ': ' . $v['obtained'])
                            ->implode(' | ');
                        $componentsStrings[] = $sub['name'] . ' (' . $subStr . ')';
                    }
                    $componentStr = implode("\n", $componentsStrings);
                } else {
                    $componentStr = collect($d['components'])
                        ->map(fn($v, $k) => strtoupper($k) . ': ' . $v['obtained'])
                        ->implode(' | ');
                }

                $rows[] = [
                    'sno'              => $sno++,
                    'subject_name'     => $d['subject_name'],
                    'component_marks'  => $componentStr,
                    'subject_total'    => $d['obtained'],
                    'subject_full'     => $d['full'],
                    'subject_grade'    => $d['grade'],
                    'subject_gpa'      => number_format($d['gpa'], 2),
                    'subject_status'   => $d['is_passed'] ? 'Pass' : 'Fail',
                ];
            }

            $processor->cloneRowAndSetValues('sno', $rows);
        } catch (\Exception) {
            // Template doesn't use subject row cloning — skip
        }
    }
}
