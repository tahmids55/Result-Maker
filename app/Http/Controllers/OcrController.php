<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOCRJob;
use App\Models\Mark;
use App\Models\OcrImport;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OcrController extends Controller
{
    public function index()
    {
        $imports = OcrImport::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
        return view('ocr.index', compact('imports'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image'    => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'language' => 'required|in:eng,ben,eng+ben',
        ]);

        $path = $request->file('image')->store('ocr', 'local');

        $import = OcrImport::create([
            'user_id'    => Auth::id(),
            'image_path' => $path,
            'status'     => 'pending',
            'language'   => $request->language,
        ]);

        ProcessOCRJob::dispatch($import);

        return redirect()->route('ocr.show', $import)
            ->with('success', 'Image uploaded. OCR processing started.');
    }

    public function show(OcrImport $import)
    {
        return view('ocr.show', compact('import'));
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|file|mimes:zip|max:51200',
            'language' => 'required|in:eng,ben,eng+ben',
        ]);

        $zipPath = $request->file('zip_file')->store('ocr/zips', 'local');
        $zipFull = Storage::disk('local')->path($zipPath);

        $zip     = new \ZipArchive();
        $count   = 0;

        if ($zip->open($zipFull) === true) {
            $extractDir = Storage::disk('local')->path('ocr/extracted_' . time());
            $zip->extractTo($extractDir);
            $zip->close();

            foreach (glob("{$extractDir}/*.{jpg,jpeg,png}", GLOB_BRACE) as $imgFile) {
                $ext = pathinfo($imgFile, PATHINFO_EXTENSION);
                $relative = 'ocr/' . \Illuminate\Support\Str::random(40) . '.' . $ext;
                rename($imgFile, Storage::disk('local')->path($relative));

                $import = OcrImport::create([
                    'user_id'    => Auth::id(),
                    'image_path' => $relative,
                    'status'     => 'pending',
                    'language'   => $request->language,
                ]);

                ProcessOCRJob::dispatch($import);
                $count++;
            }
        }

        return redirect()->route('ocr.index')
            ->with('success', "{$count} images queued for OCR processing.");
    }

    /**
     * Save confirmed OCR data as marks into the database.
     */
    public function saveMarks(Request $request, OcrImport $import)
    {
        $request->validate([
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
            'subject_id' => ['required', \Illuminate\Validation\Rule::exists('subjects', 'id')->where('user_id', auth()->id())],
            'rows'       => 'required|array',
            'rows.*.roll'       => 'required|integer',
            'rows.*.components' => 'required|array',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        $saved   = 0;

        foreach ($request->rows as $row) {
            $student = Student::where('roll', $row['roll'])
                ->where('class_id', $subject->class_id)
                ->where('section_id', $subject->section_id)
                ->first();

            if (!$student) continue;

            foreach ($row['components'] as $component => $obtained) {
                $componentConfig = $subject->exam_components[$component] ?? null;
                if (!$componentConfig) continue;

                Mark::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'exam_id'    => $request->exam_id,
                        'component'  => $component,
                    ],
                    [
                        'obtained_marks' => min((float) $obtained, $componentConfig['full']),
                        'full_marks'     => $componentConfig['full'],
                        'pass_marks'     => $componentConfig['pass'],
                    ]
                );
                $saved++;
            }
        }

        return redirect()->route('ocr.index')
            ->with('success', "{$saved} mark entries saved from OCR data.");
    }
}
