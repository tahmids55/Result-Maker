<?php

namespace App\Http\Controllers;

use App\Jobs\SendSMSJob;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\SmsLog;
use App\Models\Student;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct(private SmsService $smsService) {}

    public function index()
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams   = Exam::orderByDesc('year')->get();
        $logs    = SmsLog::with('student')->latest()->paginate(20);
        return view('sms.index', compact('classes', 'exams', 'logs'));
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'exam_id'    => ['required', \Illuminate\Validation\Rule::exists('exams', 'id')->where('user_id', auth()->id())],
            'channel'    => 'required|in:sms,whatsapp',
            'message_template' => 'required|string|max:500',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $exam     = Exam::findOrFail($request->exam_id);
        $students = Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->whereNotNull('phone')
            ->with('results')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students with phone numbers found.');
        }

        $count = 0;
        foreach ($students as $student) {
            $result  = $student->getResultForExam($exam->id);
            $message = $this->buildMessage($request->message_template, $student, $exam, $result);

            $job = new SendSMSJob(
                phone:   $student->phone,
                message: $message,
                channel: $request->channel,
                student: $student
            );

            if ($request->filled('scheduled_at')) {
                $job->delay(now()->parse($request->scheduled_at));
            }

            dispatch($job);
            $count++;
        }

        return back()->with('success', "{$count} messages queued for dispatch.");
    }

    public function sendSingle(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string|max:20',
            'message' => 'required|string|max:500',
            'channel' => 'required|in:sms,whatsapp',
        ]);

        $student = $request->filled('student_id')
            ? Student::find($request->student_id)
            : null;

        SendSMSJob::dispatch($request->phone, $request->message, $request->channel, $student);

        return back()->with('success', 'Message queued successfully.');
    }

    public function logs()
    {
        $logs = SmsLog::with('student')
            ->latest()
            ->paginate(30);
        return view('sms.logs', compact('logs'));
    }

    private function buildMessage(string $template, Student $student, Exam $exam, $result): string
    {
        $replacements = [
            '{student_name}' => $student->name,
            '{roll}'         => $student->roll,
            '{exam_name}'    => $exam->name,
            '{exam_year}'    => $exam->year,
            '{grade}'        => $result?->grade ?? 'N/A',
            '{gpa}'          => $result ? number_format($result->gpa, 2) : 'N/A',
            '{total}'        => $result?->total_marks ?? 'N/A',
            '{percentage}'   => $result ? number_format($result->percentage, 2) . '%' : 'N/A',
            '{status}'       => $result?->is_passed ? 'PASSED' : 'FAILED',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
