<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentConversionService
{
    /**
     * Merge multiple docx files into a single docx file.
     */
    public function mergeDocx(array $inputPaths, string $outputPath): bool
    {
        if (count($inputPaths) === 0) {
            return false;
        }
        if (count($inputPaths) === 1) {
            copy($inputPaths[0], $outputPath);
            return true;
        }

        $script = base_path('scripts/merge_docx.py');
        $python = base_path('venv/bin/python'); // Assuming venv is created at root

        $cmd = escapeshellarg($python) . " " . escapeshellarg($script) . " " . escapeshellarg($outputPath);
        foreach ($inputPaths as $in) {
            $cmd .= " " . escapeshellarg($in);
        }

        exec($cmd, $output, $returnVar);
        
        if ($returnVar !== 0) {
            Log::error("Failed to merge DOCX files: " . implode("\n", $output));
            return false;
        }
        
        return true;
    }

    /**
     * Convert docx to pdf using libreoffice headless.
     */
    public function convertDocxToPdf(string $inputDocxPath, string $outputDir): ?string
    {
        $cmd = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg($outputDir) . " " . escapeshellarg($inputDocxPath);
        exec($cmd, $output, $returnVar);

        if ($returnVar !== 0) {
            Log::error("Failed to convert to PDF: " . implode("\n", $output));
            return null;
        }

        $filename = pathinfo($inputDocxPath, PATHINFO_FILENAME);
        $pdfPath = rtrim($outputDir, '/') . '/' . $filename . '.pdf';
        
        return file_exists($pdfPath) ? $pdfPath : null;
    }
}
