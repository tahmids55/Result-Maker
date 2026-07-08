<?php

namespace App\Jobs;

use App\Models\Exam;
use App\Models\MarksheetTemplate;
use App\Models\Student;
use App\Services\MarksheetGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMarksheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly Student           $student,
        public readonly Exam              $exam,
        public readonly MarksheetTemplate $template,
    ) {}

    public function handle(MarksheetGenerationService $service): void
    {
        try {
            $service->generateForStudent($this->student, $this->exam, $this->template);
        } catch (\Exception $e) {
            Log::error("Marksheet generation failed for student {$this->student->id}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
