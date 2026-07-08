<?php

namespace App\Jobs;

use App\Models\OcrImport;
use App\Services\OcrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOCRJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 180;

    public function __construct(public readonly OcrImport $import) {}

    public function handle(OcrService $service): void
    {
        try {
            $service->processImage($this->import);
        } catch (\Exception $e) {
            Log::error("OCR processing failed for import {$this->import->id}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
