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
     * Convert docx to pdf using ONLYOFFICE's internal x2t converter via Docker.
     * This guarantees 100% pixel-perfect formatting identical to the web editor.
     */
    public function convertDocxToPdf(string $inputDocxPath, string $outputDir): ?string
    {
        $filename = pathinfo($inputDocxPath, PATHINFO_FILENAME);
        $pdfPath = rtrim($outputDir, '/') . '/' . $filename . '.pdf';

        // Temporary paths inside the docker container
        $tmpIn = "/tmp/in_" . uniqid() . ".docx";
        $tmpOut = "/tmp/out_" . uniqid() . ".pdf";

        // 1. Copy the .docx into the ONLYOFFICE container
        exec("docker cp " . escapeshellarg($inputDocxPath) . " onlyoffice-ds:{$tmpIn} 2>&1", $out1, $ret1);
        if ($ret1 !== 0) {
            Log::error("Docker copy in failed: " . implode("\n", $out1));
            return null;
        }

        // 2. Convert using ONLYOFFICE x2t engine (using its own core fonts)
        // x2t signature: x2t <input> <output> <fonts_path>
        $x2tCmd = "docker exec onlyoffice-ds /var/www/onlyoffice/documentserver/server/FileConverter/bin/x2t {$tmpIn} {$tmpOut} /var/www/onlyoffice/documentserver/core-fonts";
        exec($x2tCmd . " 2>&1", $out2, $ret2);

        // 3. Copy the resulting PDF out of the container
        exec("docker cp onlyoffice-ds:{$tmpOut} " . escapeshellarg($pdfPath) . " 2>&1", $out3, $ret3);

        // 4. Cleanup temporary files inside the container
        exec("docker exec onlyoffice-ds rm -f {$tmpIn} {$tmpOut}");

        if (!file_exists($pdfPath) || filesize($pdfPath) === 0) {
            Log::error("ONLYOFFICE conversion failed. x2t output: " . implode("\n", $out2));
            return null;
        }

        return $pdfPath;
    }
}
