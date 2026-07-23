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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateClassMarksheetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;
    public $tries   = 1; // Do NOT retry — it causes the progress bar to loop

    public function __construct(
        public string $batchId,
        public int $classId,
        public int $sectionId,
        public int $examId,
        public int $templateId,
        public string $outputMode,
        public string $outputFormat,
        public int $userId,
        public ?int $rollStart = null,
        public ?int $rollEnd = null
    ) {}

    private function updateProgress(string $stage, int $current, int $total, ?string $detail = null): void
    {
        Cache::put("marksheet_batch:{$this->batchId}", [
            'stage'   => $stage,
            'current' => $current,
            'total'   => $total,
            'pct'     => $total > 0 ? round(($current / $total) * 100) : 0,
            'detail'  => $detail,
            'status'  => 'processing',
        ], 3600);
    }

    public function handle(MarksheetGenerationService $service, DocumentConversionService $conversionService): void
    {
        try {
            $exam     = Exam::findOrFail($this->examId);
            $template = MarksheetTemplate::findOrFail($this->templateId);
            $class    = SchoolClass::findOrFail($this->classId);
            $section  = Section::findOrFail($this->sectionId);

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
                $this->markDone('No students found.', null);
                return;
            }

            $totalStudents = $students->count();

            // === Phase 1: Generate individual DOCX files ===
            $generatedDocxPaths = [];
            foreach ($students as $i => $student) {
                $this->updateProgress(
                    'Generating marksheets',
                    $i + 1,
                    $totalStudents,
                    "Processing {$student->name} (Roll {$student->roll})"
                );

                $docPath = $service->generateForStudent($student, $exam, $template, false);
                $generatedDocxPaths[] = Storage::disk('local')->path($docPath);
            }

            // Create temp directory via Storage facade (respects permissions)
            $tempDirRelative = "batch_temp/" . Str::uuid();
            Storage::disk('local')->makeDirectory($tempDirRelative);
            $tempDir = Storage::disk('local')->path($tempDirRelative);

            $finalFilePath = null;
            $batchName = "{$class->name} - {$section->name} ({$exam->name})";

            // === Phase 2: Process according to mode ===
            if ($this->outputMode === 'combined') {
                $this->updateProgress('Merging documents', $totalStudents, $totalStudents, 'Combining all marksheets into one file...');

                $mergedDocxPath = $tempDir . "/combined.docx";
                $merged = $conversionService->mergeDocx($generatedDocxPaths, $mergedDocxPath);

                if (!$merged) {
                    $this->markFailed('Failed to merge DOCX files.');
                    return;
                }

                if ($this->outputFormat === 'pdf') {
                    $this->updateProgress('Converting to PDF', $totalStudents, $totalStudents, 'Running ONLYOFFICE conversion...');

                    $pdfPath = $conversionService->convertDocxToPdf($mergedDocxPath, $tempDir);
                    if (!$pdfPath) {
                        $this->markFailed('PDF conversion failed. Is ONLYOFFICE Document Server running?');
                        return;
                    }
                    $finalFileName = "marksheets/{$exam->id}/batch_" . uniqid() . ".pdf";
                    $this->moveToStorage($pdfPath, $finalFileName);
                    $finalFilePath = $finalFileName;
                } else {
                    $finalFileName = "marksheets/{$exam->id}/batch_" . uniqid() . ".docx";
                    $this->moveToStorage($mergedDocxPath, $finalFileName);
                    $finalFilePath = $finalFileName;
                }
            } else {
                // Individual mode (ZIP)
                $zipFilePath = $tempDir . "/marksheets.zip";
                $filesToZip  = [];

                // First, prepare all files (convert to PDF if needed)
                foreach ($generatedDocxPaths as $index => $docxPath) {
                    $student = $students[$index];

                    $this->updateProgress(
                        $this->outputFormat === 'pdf' ? 'Converting to PDF' : 'Packaging files',
                        $index + 1,
                        $totalStudents,
                        "Preparing {$student->name}..."
                    );

                    if ($this->outputFormat === 'pdf') {
                        $pdfPath = $conversionService->convertDocxToPdf($docxPath, $tempDir);
                        if ($pdfPath && file_exists($pdfPath)) {
                            $filesToZip[] = ['path' => $pdfPath, 'name' => "{$student->roll}_{$student->name}.pdf"];
                        }
                    } else {
                        if (file_exists($docxPath)) {
                            $filesToZip[] = ['path' => $docxPath, 'name' => "{$student->roll}_{$student->name}.docx"];
                        }
                    }
                }

                // Now create the ZIP from collected files
                $this->updateProgress('Creating ZIP', $totalStudents, $totalStudents, 'Building ZIP archive...');

                $zip = new \ZipArchive();
                $result = $zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

                if ($result !== true) {
                    $this->markFailed("Could not create ZIP archive (error code: {$result}).");
                    return;
                }

                foreach ($filesToZip as $file) {
                    $zip->addFile($file['path'], $file['name']);
                }

                $closed = $zip->close();

                if (!$closed || !file_exists($zipFilePath) || filesize($zipFilePath) === 0) {
                    $this->markFailed('ZIP archive creation failed.');
                    return;
                }

                $finalFileName = "marksheets/{$exam->id}/batch_" . uniqid() . ".zip";
                $this->moveToStorage($zipFilePath, $finalFileName);
                $finalFilePath = $finalFileName;
            }

            // === Phase 3: Save to History ===
            $downloadId = null;
            if ($finalFilePath) {
                $record = GeneratedMarksheet::create([
                    'student_id'   => null,
                    'batch_name'   => $batchName,
                    'exam_id'      => $exam->id,
                    'template_id'  => $template->id,
                    'file_path'    => $finalFilePath,
                    'file_type'    => $this->outputMode === 'combined' ? $this->outputFormat : 'zip',
                    'generated_at' => now(),
                    'user_id'      => $this->userId,
                ]);
                $downloadId = $record->id;
            }

            $this->markDone("Successfully generated for {$totalStudents} students.", $downloadId);

            // Cleanup temp dir
            exec("rm -rf " . escapeshellarg($tempDir));

        } catch (\Exception $e) {
            Log::error("Batch marksheet generation failed [{$this->batchId}]: " . $e->getMessage());
            $this->markFailed('An unexpected error occurred: ' . Str::limit($e->getMessage(), 150));
        }
    }

    /**
     * Move a file into Laravel Storage safely.
     */
    private function moveToStorage(string $sourcePath, string $storageKey): void
    {
        Storage::disk('local')->makeDirectory(dirname($storageKey));
        // Use copy + unlink instead of file_get_contents to avoid memory issues with large files
        copy($sourcePath, Storage::disk('local')->path($storageKey));
    }

    private function markDone(string $message, ?int $downloadId): void
    {
        Cache::put("marksheet_batch:{$this->batchId}", [
            'stage'       => 'Done',
            'current'     => 1,
            'total'       => 1,
            'pct'         => 100,
            'detail'      => $message,
            'status'      => 'done',
            'download_id' => $downloadId,
        ], 3600);
    }

    private function markFailed(string $message): void
    {
        Cache::put("marksheet_batch:{$this->batchId}", [
            'stage'   => 'Error',
            'current' => 0,
            'total'   => 0,
            'pct'     => 0,
            'detail'  => $message,
            'status'  => 'failed',
        ], 3600);
    }
}
