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
        $teacherSubjects = auth()->user()->isTeacher() 
            ? auth()->user()->assignedSubjects()->with(['schoolClass', 'section'])->get() 
            : collect();
            
        return view('results.index', compact('classes', 'exams', 'teacherSubjects'));
    }

    public function classResult(Request $request)
    {
        $request->validate([
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->user()->owner_id)],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
            'subject_id' => ['nullable', \Illuminate\Validation\Rule::exists('subjects', 'id')->where('user_id', auth()->user()->owner_id)],
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

        // Subject-wise average GPA for the Subject Performance chart
        $subjectAverages = [];
        foreach ($results as $r) {
            foreach ($r->subject_details ?? [] as $detail) {
                $name = $detail['subject_name'] ?? 'Unknown';
                if (!isset($subjectAverages[$name])) {
                    $subjectAverages[$name] = ['total_gpa' => 0, 'count' => 0];
                }
                $subjectAverages[$name]['total_gpa'] += $detail['gpa'] ?? 0;
                $subjectAverages[$name]['count']++;
            }
        }
        $subjectAverages = collect($subjectAverages)->map(fn($v, $k) => [
            'name' => $k,
            'avg_gpa' => $v['count'] > 0 ? round($v['total_gpa'] / $v['count'], 2) : 0,
        ])->values();

        $teacherSubject = null;
        if ($request->filled('subject_id') && auth()->user()->isTeacher()) {
            $teacherSubject = \App\Models\Subject::findOrFail($request->subject_id);
        }

        return view('results.class', compact(
            'results', 'exam', 'class', 'section',
            'totalStudents', 'passedStudents', 'gradeDistrib', 'subjectAverages', 'teacherSubject'
        ));
    }

    public function recalculate(Request $request)
    {
        $request->validate([
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->user()->owner_id)],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
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
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->user()->owner_id)],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
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
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->user()->owner_id)],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
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
