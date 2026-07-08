<?php

namespace App\Services;

use App\Models\OcrImport;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrService
{
    /**
     * Process an uploaded image and extract text.
     */
    public function processImage(OcrImport $import): array
    {
        $import->update(['status' => 'processing']);

        try {
            $imagePath = Storage::disk('local')->path($import->image_path);

            $ocr = new TesseractOCR($imagePath);
            $ocr->executable(config('services.tesseract.path', '/usr/bin/tesseract'));
            $ocr->lang($import->language ?? 'eng');
            $ocr->psm(6); // Assume uniform block of text

            $rawText = $ocr->run();
            $parsed  = $this->parseExtractedText($rawText);

            $import->update([
                'extracted_data' => [
                    'raw_text' => $rawText,
                    'parsed'   => $parsed,
                ],
                'status' => 'processed',
            ]);

            return $parsed;
        } catch (\Exception $e) {
            $import->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Naively parse OCR output into structured rows.
     * Lines like: "101 Rakib Hasan 35 52 87"
     */
    private function parseExtractedText(string $text): array
    {
        $lines  = array_filter(explode("\n", trim($text)));
        $parsed = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Try to extract roll + name + numbers
            if (preg_match('/^(\d+)\s+(.+?)\s+([\d\s]+)$/', $line, $matches)) {
                $numbers = array_map('intval', preg_split('/\s+/', trim($matches[3])));
                $parsed[] = [
                    'roll'   => (int) $matches[1],
                    'name'   => trim($matches[2]),
                    'marks'  => $numbers,
                    'raw'    => $line,
                ];
            } else {
                // Unrecognised line — store as-is for manual review
                $parsed[] = ['raw' => $line, 'roll' => null, 'name' => null, 'marks' => []];
            }
        }

        return $parsed;
    }
}
