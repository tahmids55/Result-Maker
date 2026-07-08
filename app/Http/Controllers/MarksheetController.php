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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarksheetController extends Controller
{
    public function __construct(private MarksheetGenerationService $service) {}

    public function index()
    {
        $classes   = SchoolClass::orderBy('sort_order')->get();
        $exams     = Exam::orderByDesc('year')->get();
        $templates = MarksheetTemplate::orderByDesc('is_default')->get();
        return view('marksheets.index', compact('classes', 'exams', 'templates'));
    }

    /**
     * Dispatch batch generation jobs for all students in a class-section.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'class_id'    => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id'  => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'     => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
            'template_id' => ['required', \Illuminate\Validation\Rule::exists('marksheet_templates', 'id')->where('user_id', auth()->id())],
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
            return back()->with('error', 'No students found for the selected class and section.');
        }

        foreach ($students as $student) {
            GenerateMarksheetJob::dispatch($student, $exam, $template);
        }

        return back()->with('success', "Generation queued for {$students->count()} students. Check back shortly for downloads.");
    }

    /**
     * Generate immediately (synchronous, for small classes).
     */
    public function generateSync(Request $request)
    {
        $request->validate([
            'class_id'    => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id'  => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'     => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
            'template_id' => ['required', \Illuminate\Validation\Rule::exists('marksheet_templates', 'id')->where('user_id', auth()->id())],
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

        $zip = new \ZipArchive();
        $zipDir = "marksheets/{$exam->id}";
        Storage::disk('local')->makeDirectory($zipDir);
        $zipFileName = "class_{$request->class_id}_section_{$request->section_id}_custom_" . time() . ".zip";
        $zipPath = "{$zipDir}/{$zipFileName}";
        $fullZipPath = Storage::disk('local')->path($zipPath);

        if ($zip->open($fullZipPath, \ZipArchive::CREATE) !== true) {
            return back()->with('error', "Cannot create zip archive.");
        }

        foreach ($students as $student) {
            $docPath = $this->service->generateForStudent($student, $exam, $template);
            $fullDocPath = Storage::disk('local')->path($docPath);
            $filename = "{$student->roll}_{$student->name}.docx";
            $zip->addFile($fullDocPath, $filename);
        }

        $zip->close();

        return Storage::disk('local')->download($zipPath, "marksheets_{$exam->name}_{$exam->year}_Custom.zip");
    }

    public function download(GeneratedMarksheet $marksheet)
    {
        $path = Storage::disk('local')->path($marksheet->file_path);
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }
        $filename = "marksheet_{$marksheet->student->name}_{$marksheet->exam->name}.docx";
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
