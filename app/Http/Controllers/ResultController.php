<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Services\ResultCalculationService;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function __construct(private ResultCalculationService $calculator) {}

    public function index()
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams   = Exam::orderByDesc('year')->get();
        return view('results.index', compact('classes', 'exams'));
    }

    public function classResult(Request $request)
    {
        $request->validate([
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $class   = SchoolClass::findOrFail($request->class_id);
        $section = Section::findOrFail($request->section_id);

        $studentIds = Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->pluck('id');

        $results = Result::with('student')
            ->whereIn('student_id', $studentIds)
            ->where('exam_id', $exam->id)
            ->orderBy('rank')
            ->get();

        // Stats
        $totalStudents  = $studentIds->count();
        $passedStudents = $results->where('is_passed', true)->count();
        $gradeDistrib   = $results->groupBy('grade')->map->count();

        return view('results.class', compact(
            'results', 'exam', 'class', 'section',
            'totalStudents', 'passedStudents', 'gradeDistrib'
        ));
    }

    public function recalculate(Request $request)
    {
        $request->validate([
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $this->calculator->calculateForClass(
            $request->class_id,
            $request->section_id,
            $exam
        );

        return back()->with('success', 'Results recalculated and ranks updated.');
    }

    public function studentResult(Student $student, Exam $exam)
    {
        $result = $student->getResultForExam($exam->id);
        if (!$result) {
            $result = $this->calculator->calculateForStudent($student, $exam);
        }
        return view('results.student', compact('student', 'exam', 'result'));
    }

    public function meritList(Request $request)
    {
        $request->validate([
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $class   = SchoolClass::findOrFail($request->class_id);
        $section = Section::findOrFail($request->section_id);

        $studentIds = Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->pluck('id');

        $results = Result::with('student')
            ->whereIn('student_id', $studentIds)
            ->where('exam_id', $exam->id)
            ->orderBy('rank')
            ->get();

        return view('results.merit', compact('results', 'exam', 'class', 'section'));
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $class   = SchoolClass::findOrFail($request->class_id);
        $section = Section::findOrFail($request->section_id);

        $studentIds = Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->pluck('id');

        $results = Result::with('student')
            ->whereIn('student_id', $studentIds)
            ->where('exam_id', $exam->id)
            ->orderBy('rank')
            ->get();

        // Build CSV response
        $filename = "results_{$class->name}_{$section->name}_{$exam->name}_{$exam->year}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($results) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Rank', 'Roll', 'Name', 'Total', 'Full', 'Percentage', 'GPA', 'Grade', 'Division', 'Status']);

            foreach ($results as $r) {
                fputcsv($handle, [
                    $r->rank,
                    $r->student->roll,
                    $r->student->name,
                    $r->total_marks,
                    $r->full_marks,
                    number_format($r->percentage, 2),
                    number_format($r->gpa, 2),
                    $r->grade,
                    $r->division,
                    $r->is_passed ? 'Pass' : 'Fail',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
