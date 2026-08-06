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
            'section_id' => ['nullable', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
            'subject_id' => ['nullable', \Illuminate\Validation\Rule::exists('subjects', 'id')->where('user_id', auth()->user()->owner_id)],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $class   = SchoolClass::findOrFail($request->class_id);
        $section = $request->section_id ? Section::findOrFail($request->section_id) : null;

        $studentQuery = Student::where('class_id', $request->class_id);
        if ($request->section_id) {
            $studentQuery->where('section_id', $request->section_id);
        }
        $studentIds = $studentQuery->pluck('id');

        $results = Result::with('student.section')
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
            'section_id' => ['nullable', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $this->calculator->calculateForClass(
            $request->class_id,
            $request->section_id, // can be null
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
            'section_id' => ['nullable', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $class   = SchoolClass::findOrFail($request->class_id);
        $section = $request->section_id ? Section::findOrFail($request->section_id) : null;

        $studentQuery = Student::where('class_id', $request->class_id);
        if ($request->section_id) {
            $studentQuery->where('section_id', $request->section_id);
        }
        $studentIds = $studentQuery->pluck('id');

        $results = Result::with('student.section')
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
            'section_id' => ['nullable', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $class   = SchoolClass::findOrFail($request->class_id);
        $section = $request->section_id ? Section::findOrFail($request->section_id) : null;

        $studentQuery = Student::where('class_id', $request->class_id);
        if ($request->section_id) {
            $studentQuery->where('section_id', $request->section_id);
        }
        $studentIds = $studentQuery->pluck('id');

        $results = Result::with('student.section')
            ->whereIn('student_id', $studentIds)
            ->where('exam_id', $exam->id)
            ->orderBy('rank')
            ->get();

        // Build CSV response
        $sectionName = $section ? $section->name : 'All_Sections';
        $filename = "results_{$class->name}_{$sectionName}_{$exam->name}_{$exam->year}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($results) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Rank', 'Roll', 'Name', 'Section', 'Total', 'Full', 'Percentage', 'GPA', 'Grade', 'Status']);

            foreach ($results as $r) {
                fputcsv($handle, [
                    $r->rank,
                    $r->student->roll,
                    $r->student->name,
                    $r->student->section->name ?? '-',
                    $r->total_marks,
                    $r->full_marks,
                    number_format($r->percentage, 2),
                    number_format($r->gpa, 2),
                    $r->grade,
                    $r->is_passed ? 'Pass' : 'Fail',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function tabulationSheet(Request $request)
    {
        $request->validate([
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->user()->owner_id)],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->user()->owner_id)],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->user()->owner_id)],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $class   = SchoolClass::findOrFail($request->class_id);
        $section = $request->section_id ? Section::findOrFail($request->section_id) : null;

        $studentQuery = Student::where('class_id', $request->class_id)->orderBy('roll');
        if ($request->section_id) {
            $studentQuery->where('section_id', $request->section_id);
        }
        $studentIds = $studentQuery->pluck('id');
        $students = Student::whereIn('id', $studentIds)->orderBy('roll')->get();

        $results = Result::with('student.section')
            ->whereIn('student_id', $studentIds)
            ->where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');

        $subjectsQuery = \App\Models\Subject::with('subSubjects')->where('class_id', $class->id)->orderBy('sort_order');
        if ($section) {
            $subjectsQuery->where('section_id', $section->id);
        } else {
            $firstSectionId = Section::where('class_id', $class->id)->value('id');
            if ($firstSectionId) {
                $subjectsQuery->where('section_id', $firstSectionId);
            }
        }
        $subjects = $subjectsQuery->get();

        $school = \App\Models\School::first();

        // Dynamically adjust students per page based on subject count to avoid clutter
        $perPage = $subjects->count() >= 9 ? 8 : 10;
        
        // Break students into chunks based on per_page
        $chunks = $students->chunk($perPage);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('results.tabulation_pdf', compact(
            'chunks', 'results', 'exam', 'class', 'section', 'subjects', 'school'
        ))
        ->setPaper('a4', 'landscape')
        ->setOption('defaultFont', 'Helvetica')
        ->setOption('isFontSubsettingEnabled', true)
        ->setOption('margin_top', 10)
        ->setOption('margin_bottom', 10)
        ->setOption('margin_left', 10)
        ->setOption('margin_right', 10);

        $sectionName = $section ? $section->name : 'All_Sections';
        $filename = "Tabulation_Sheet_{$class->name}_{$sectionName}.pdf";
        $filename = str_replace(['/', '\\'], '_', $filename);
        return $pdf->download($filename);
    }
}
