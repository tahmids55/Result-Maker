<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\Student;
use Twilio\Rest\Client as TwilioClient;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private ?TwilioClient $twilio = null;

    private function getClient(): TwilioClient
    {
        if (!$this->twilio) {
            $this->twilio = new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
        }
        return $this->twilio;
    }

    /**
     * Send an SMS to a phone number.
     */
    public function sendSms(string $to, string $message, ?Student $student = null): SmsLog
    {
        $log = SmsLog::create([
            'student_id'   => $student?->id,
            'phone_number' => $to,
            'message'      => $message,
            'channel'      => 'sms',
            'status'       => 'pending',
        ]);

        try {
            $twilio  = $this->getClient();
            $result  = $twilio->messages->create($to, [
                'from' => config('services.twilio.from'),
                'body' => $message,
            ]);

            $log->update([
                'status'              => 'sent',
                'provider_message_id' => $result->sid,
                'sent_at'             => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('SMS send failed: ' . $e->getMessage());
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $log;
    }

    /**
     * Send a WhatsApp message.
     */
    public function sendWhatsApp(string $to, string $message, ?Student $student = null): SmsLog
    {
        $log = SmsLog::create([
            'student_id'   => $student?->id,
            'phone_number' => $to,
            'message'      => $message,
            'channel'      => 'whatsapp',
            'status'       => 'pending',
        ]);

        try {
            $twilio = $this->getClient();
            $result = $twilio->messages->create("whatsapp:{$to}", [
                'from' => 'whatsapp:' . config('services.twilio.from'),
                'body' => $message,
            ]);

            $log->update([
                'status'              => 'sent',
                'provider_message_id' => $result->sid,
                'sent_at'             => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $log;
    }

    /**
     * Build a result notification message for a student.
     */
    public function buildResultMessage(Student $student, string $examName, string $grade, float $gpa, bool $passed): string
    {
        $status = $passed ? 'PASSED' : 'FAILED';
        return "MarksCraft Result\n{$student->name} (Roll: {$student->roll})\nExam: {$examName}\nGrade: {$grade} | GPA: {$gpa}\nResult: {$status}";
    }
}
