<?php

namespace App\Jobs;

use App\Models\Exam;
use App\Models\MarksheetTemplate;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\GeneratedMarksheet;
use App\Services\MarksheetGenerationService;
use App\Services\DocumentConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateClassMarksheetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout for big classes

    public function __construct(
        public int $classId,
        public int $sectionId,
        public int $examId,
        public int $templateId,
        public string $outputMode,
        public string $outputFormat,
        public ?int $rollStart = null,
        public ?int $rollEnd = null
    ) {}

    public function handle(MarksheetGenerationService $service, DocumentConversionService $conversionService): void
    {
        $exam = Exam::findOrFail($this->examId);
        $template = MarksheetTemplate::findOrFail($this->templateId);
        $class = SchoolClass::findOrFail($this->classId);
        $section = Section::findOrFail($this->sectionId);

        $query = Student::where('class_id', $this->classId)
            ->where('section_id', $this->sectionId)
            ->orderBy('roll');

        if ($this->rollStart) {
            $query->where('roll', '>=', $this->rollStart);
        }
        if ($this->rollEnd) {
            $query->where('roll', '<=', $this->rollEnd);
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return;
        }

        // 1. Generate individual DOCX (do not save individual records to DB)
        $generatedDocxPaths = [];
        foreach ($students as $student) {
            $docPath = $service->generateForStudent($student, $exam, $template, false);
            $generatedDocxPaths[] = Storage::disk('local')->path($docPath);
        }

        $tempDir = Storage::disk('local')->path("temp_generation/" . Str::uuid());
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $finalFilePath = null;
        $batchName = "{$class->name} - {$section->name} ({$exam->name})";

        // 2. Process according to mode
        if ($this->outputMode === 'combined') {
            $mergedDocxPath = $tempDir . "/combined.docx";
            $merged = $conversionService->mergeDocx($generatedDocxPaths, $mergedDocxPath);
            
            if (!$merged) {
                return;
            }

            if ($this->outputFormat === 'pdf') {
                $pdfPath = $conversionService->convertDocxToPdf($mergedDocxPath, $tempDir);
                if ($pdfPath) {
                    $finalFileName = "marksheets/{$exam->id}/batch_" . uniqid() . ".pdf";
                    Storage::disk('local')->put($finalFileName, file_get_contents($pdfPath));
                    $finalFilePath = $finalFileName;
                }
            } else {
                $finalFileName = "marksheets/{$exam->id}/batch_" . uniqid() . ".docx";
                Storage::disk('local')->put($finalFileName, file_get_contents($mergedDocxPath));
                $finalFilePath = $finalFileName;
            }
        } else {
            // Individual mode (ZIP)
            $zip = new \ZipArchive();
            $zipFilePath = $tempDir . "/marksheets.zip";
            
            if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
                foreach ($generatedDocxPaths as $index => $docxPath) {
                    $student = $students[$index];
                    if ($this->outputFormat === 'pdf') {
                        $pdfPath = $conversionService->convertDocxToPdf($docxPath, $tempDir);
                        if ($pdfPath) {
                            $zip->addFile($pdfPath, "{$student->roll}_{$student->name}.pdf");
                        }
                    } else {
                        $zip->addFile($docxPath, "{$student->roll}_{$student->name}.docx");
                    }
                }
                $zip->close();
                
                $finalFileName = "marksheets/{$exam->id}/batch_" . uniqid() . ".zip";
                Storage::disk('local')->put($finalFileName, file_get_contents($zipFilePath));
                $finalFilePath = $finalFileName;
            }
        }

        // 3. Save to History
        if ($finalFilePath) {
            GeneratedMarksheet::create([
                'student_id' => null, // null for batch
                'batch_name' => $batchName,
                'exam_id' => $exam->id,
                'template_id' => $template->id,
                'file_path' => $finalFilePath,
                'file_type' => $this->outputMode === 'combined' ? $this->outputFormat : 'zip',
                'generated_at' => now(),
            ]);
        }
        
        // Cleanup temp dir
        exec("rm -rf " . escapeshellarg($tempDir));
    }
}
