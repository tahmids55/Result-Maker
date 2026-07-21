<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateMarksheetJob;
use App\Models\Exam;
use App\Models\GeneratedMarksheet;
use App\Models\MarksheetTemplate;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Services\MarksheetGenerationService;
use App\Services\DocumentConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarksheetController extends Controller
{
    public function __construct(
        private MarksheetGenerationService $service,
        private DocumentConversionService $conversionService
    ) {}

    public function index()
    {
        $classes   = SchoolClass::orderBy('sort_order')->get();
        $exams     = Exam::orderByDesc('year')->get();
        $templates = MarksheetTemplate::orderByDesc('is_default')->get();
        return view('marksheets.index', compact('classes', 'exams', 'templates'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'class_id'    => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id'  => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'     => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
            'template_id' => ['required', \Illuminate\Validation\Rule::exists('marksheet_templates', 'id')->where('user_id', auth()->id())],
            'output_mode' => 'required|in:individual,combined',
            'output_format' => 'required|in:docx,pdf',
        ]);

        \App\Jobs\GenerateClassMarksheetsJob::dispatch(
            $request->class_id,
            $request->section_id,
            $request->exam_id,
            $request->template_id,
            $request->output_mode,
            $request->output_format,
            $request->roll_start,
            $request->roll_end
        );

        return back()->with('success', "Batch generation queued. Check back shortly in the History tab for your download.");
    }

    public function generateSync(Request $request)
    {
        $request->validate([
            'class_id'      => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id'    => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'       => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
            'template_id'   => ['required', \Illuminate\Validation\Rule::exists('marksheet_templates', 'id')->where('user_id', auth()->id())],
            'output_mode'   => 'required|in:individual,combined',
            'output_format' => 'required|in:docx,pdf',
        ]);

        $exam     = Exam::findOrFail($request->exam_id);
        $template = MarksheetTemplate::findOrFail($request->template_id);

        $query = Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->orderBy('roll');

        if ($request->filled('roll_start')) {
            $query->where('roll', '>=', $request->roll_start);
        }
        if ($request->filled('roll_end')) {
            $query->where('roll', '<=', $request->roll_end);
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students found for the selected criteria.');
        }

        $mode   = $request->output_mode;
        $format = $request->output_format;

        // Collect all individual DOCX files
        $generatedDocxPaths = [];
        foreach ($students as $student) {
            $docPath = $this->service->generateForStudent($student, $exam, $template, false);
            $generatedDocxPaths[] = Storage::disk('local')->path($docPath);
        }

        $tempDir = Storage::disk('local')->path("temp_generation/" . Str::uuid());
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        if ($mode === 'combined') {
            // Merge all DOCX into one
            $mergedDocxPath = $tempDir . "/combined.docx";
            $merged = $this->conversionService->mergeDocx($generatedDocxPaths, $mergedDocxPath);
            
            if (!$merged) {
                return back()->with('error', 'Failed to merge DOCX files into a single document.');
            }

            if ($format === 'pdf') {
                $pdfPath = $this->conversionService->convertDocxToPdf($mergedDocxPath, $tempDir);
                if (!$pdfPath) {
                    return back()->with('error', 'Failed to convert to PDF. Please ensure ONLYOFFICE Document Server is running.');
                }
                return response()->download($pdfPath, "marksheets_{$exam->name}_Combined.pdf")->deleteFileAfterSend(true);
            } else {
                return response()->download($mergedDocxPath, "marksheets_{$exam->name}_Combined.docx")->deleteFileAfterSend(true);
            }
        } else {
            // Individual mode (ZIP)
            $zip = new \ZipArchive();
            $zipFileName = $tempDir . "/marksheets.zip";
            
            if ($zip->open($zipFileName, \ZipArchive::CREATE) !== true) {
                return back()->with('error', "Cannot create zip archive.");
            }

            $pdfFailed = false;
            foreach ($generatedDocxPaths as $index => $docxPath) {
                $student = $students[$index];
                if ($format === 'pdf') {
                    $pdfPath = $this->conversionService->convertDocxToPdf($docxPath, $tempDir);
                    if ($pdfPath) {
                        $zip->addFile($pdfPath, "{$student->roll}_{$student->name}.pdf");
                    } else {
                        $pdfFailed = true;
                    }
                } else {
                    $zip->addFile($docxPath, "{$student->roll}_{$student->name}.docx");
                }
            }
            $zip->close();

            if ($pdfFailed) {
                return back()->with('error', 'Failed to convert one or more marksheets to PDF. Please ensure ONLYOFFICE Document Server is running.');
            }

            return response()->download($zipFileName, "marksheets_{$exam->name}_Individual.zip")->deleteFileAfterSend(true);
        }
    }

    public function download(GeneratedMarksheet $marksheet)
    {
        $path = Storage::disk('local')->path($marksheet->file_path);
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }
        
        if ($marksheet->student_id === null) {
            // It's a batch
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $filename = "marksheet_batch_{$marksheet->exam->name}.{$ext}";
        } else {
            // It's an individual
            $filename = "marksheet_{$marksheet->student->name}_{$marksheet->exam->name}.docx";
        }
        
        return response()->download($path, $filename);
    }

    public function downloadZip(Request $request)
    {
        $request->validate([
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $section = Section::findOrFail($request->section_id);

        $zipPath = "marksheets/{$exam->id}/class_{$request->class_id}_section_{$request->section_id}.zip";
        $fullPath = Storage::disk('local')->path($zipPath);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'ZIP not yet generated. Please generate marksheets first.');
        }

        return response()->download($fullPath, "marksheets_{$exam->name}_{$section->name}.zip");
    }

    public function history(Request $request)
    {
        $query = GeneratedMarksheet::with(['student', 'exam', 'template'])
            ->latest('generated_at');

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        $marksheets = $query->paginate(20)->withQueryString();
        $exams      = Exam::orderByDesc('year')->get();
        return view('marksheets.history', compact('marksheets', 'exams'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'marksheet_ids' => 'required|array',
            'marksheet_ids.*' => [\Illuminate\Validation\Rule::exists('generated_marksheets', 'id')->where('user_id', auth()->id())]
        ]);

        $marksheets = GeneratedMarksheet::whereIn('id', $request->marksheet_ids)->get();
        
        foreach ($marksheets as $marksheet) {
            Storage::disk('local')->delete($marksheet->file_path);
            $marksheet->delete();
        }

        return back()->with('success', count($marksheets) . ' marksheets deleted successfully.');
    }
}
