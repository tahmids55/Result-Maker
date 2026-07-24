<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Result;
use App\Models\SmsLog;
use App\Models\GeneratedMarksheet;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = cache()->remember('dashboard_stats_' . auth()->user()->owner_id, 300, function () {
            return [
                'total_classes'    => SchoolClass::count(),
                'total_students'   => Student::count(),
                'total_subjects'   => Subject::count(),
                'total_exams'      => Exam::count(),
                'active_exam'      => Exam::active()->latest()->first(),
                'generated_sheets' => GeneratedMarksheet::count(),
                'sms_sent'         => SmsLog::where('status', 'sent')->count(),
            ];
        });

        // Students per class for chart
        $classStats = SchoolClass::withCount('students')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($c) => ['name' => $c->name, 'count' => $c->students_count]);

        // Recent results
        $recentResults = Result::with(['student', 'exam'])
            ->latest()
            ->limit(10)
            ->get();

        $teacherSubjects = collect();
        if (auth()->user()->isTeacher()) {
            $teacherSubjects = auth()->user()->assignedSubjects()
                ->with(['schoolClass', 'section'])
                ->get()
                ->groupBy(function($subject) {
                    return $subject->schoolClass->name . ' - Section ' . $subject->section->name;
                });
        }

        return view('dashboard.index', compact('stats', 'classStats', 'recentResults', 'teacherSubjects'));
    }
}
