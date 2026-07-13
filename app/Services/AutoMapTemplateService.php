<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Exception;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Log;
use App\Models\Subject;

class AutoMapTemplateService
{
    /**
     * Reads a DOCX file, detects tables with subject names, 
     * and automatically injects placeholders (like ${subject_physics_cq})
     * into the corresponding cells based on column headers.
     */
    public function autoMap(string $absoluteFilePath): array
    {
        $results = ['success' => [], 'failed' => [], 'skipped' => []];

        $tempDir = sys_get_temp_dir() . '/docx_' . uniqid();
        mkdir($tempDir, 0777, true);

        $zip = new ZipArchive();

        if ($zip->open($absoluteFilePath) !== true) {
            throw new Exception("Cannot open DOCX");
        }

        $zip->extractTo($tempDir);
        $zip->close();

        $xmlFile = $tempDir . '/word/document.xml';

        if (!file_exists($xmlFile)) {
            throw new Exception("word/document.xml not found in DOCX");
        }

        try {
            // 1. Fetch all subjects
            $websiteSubjects = Subject::all();

            // 2. Parse DOCX
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->load($xmlFile);

            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

            $tables = $xpath->query('//w:tbl');
            
            foreach ($tables as $table) {
                $rows = $xpath->query('.//w:tr', $table);
                $columnMap = []; 
                
                foreach ($rows as $row) {
                    $cells = $xpath->query('./w:tc', $row);
                    if ($cells->length < 2) continue; // Not a subject data row

                    $firstCellText = trim(strtolower($xpath->evaluate('string(.//w:t)', $cells->item(0))));
                    
                    // Detect Header Row
                    if (str_contains($firstCellText, 'subject') || str_contains($firstCellText, 'বিষয়')) {
                        // Build column map based on headers
                        for ($i = 1; $i < $cells->length; $i++) {
                            $headerText = trim(strtolower($xpath->evaluate('string(.//w:t)', $cells->item($i))));
                            $columnMap[$i] = $this->identifyColumnType($headerText);
                        }
                        continue;
                    }

                    // Process Data Row
                    $subjectText = trim($xpath->evaluate('string(.//w:t)', $cells->item(0)));
                    if (empty($subjectText) || $subjectText === '------------------------------') continue;

                    // Match DOCX subject to Website subject
                    $matchedSubject = $this->matchSubject($subjectText, $websiteSubjects);
                    
                    if ($matchedSubject) {
                        $key = trim(strtolower($matchedSubject->code));
                        if (!$key) {
                            $key = $this->makeKey($matchedSubject->name);
                        }
                        $dbComponents = is_array($matchedSubject->exam_components) ? $matchedSubject->exam_components : [];

                        for ($i = 1; $i < $cells->length; $i++) {
                            if (!isset($columnMap[$i]) || $columnMap[$i] === null) continue;

                            $colType = $columnMap[$i];
                            $placeholder = '';

                            if (in_array($colType, ['obtained', 'grade', 'gpa', 'full'])) {
                                $suffixMap = ['obtained' => 'obt', 'grade' => 'gr', 'gpa' => 'gp', 'full' => 'fl'];
                                $placeholder = '${' . $key . '_' . $suffixMap[$colType] . '}';
                            } elseif ($colType !== null) {
                                // Match the column type to the actual DB component key
                                $actualCompKey = $this->matchComponentKey($colType, array_keys($dbComponents));
                                if ($actualCompKey) {
                                    $safeComp = $this->makeKey($actualCompKey);
                                    $placeholder = '${' . $key . '_' . $safeComp . '}';
                                }
                            }

                            if ($placeholder !== '') {
                                $this->setCellText($cells->item($i), $xpath, $placeholder);
                            }
                        }
                    }
                }
            }

            $dom->save($xmlFile);
            $results['success'][] = 'word/document.xml';

        } catch (Exception $e) {
            Log::error("Local auto mapping failed: " . $e->getMessage());
            $results['failed'][] = 'word/document.xml';
        }

        // 3. Repack zip
        $zip = new ZipArchive();
        $zip->open($absoluteFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempDir),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isDir()) continue;
            $realPath = $file->getRealPath();
            $relativePath = substr($realPath, strlen($tempDir) + 1);
            $zip->addFile($realPath, $relativePath);
        }

        $zip->close();
        $this->deleteDir($tempDir);

        return $results;
    }

    private function identifyColumnType(string $headerText): ?string
    {
        $headerText = preg_replace('/[^a-z0-9]/', '', $headerText);
        
        if ($headerText === 'cq' || str_contains($headerText, 'creative') || str_contains($headerText, 'written') || $headerText === 'wr') return 'cq';
        if (str_contains($headerText, 'mcq') || str_contains($headerText, 'objective')) return 'mcq';
        if (str_contains($headerText, 'prac') || str_contains($headerText, 'pr')) return 'practical';
        if (str_contains($headerText, 'total') || str_contains($headerText, 'tot') || str_contains($headerText, 'obt')) return 'obtained';
        if ($headerText === 'lg' || str_contains($headerText, 'grade') || str_contains($headerText, 'letter')) return 'grade';
        if ($headerText === 'gp' || str_contains($headerText, 'point') || str_contains($headerText, 'gpa')) return 'gpa';
        
        return null;
    }

    private function matchSubject(string $docxSubject, $websiteSubjects)
    {
        $normalizedDocx = $this->normalizeSubjectName($docxSubject);

        // 1. Exact or normalized match
        foreach ($websiteSubjects as $ws) {
            if ($this->normalizeSubjectName($ws->name) === $normalizedDocx) {
                return $ws;
            }
        }

        // 2. Contains match
        foreach ($websiteSubjects as $ws) {
            $normWs = $this->normalizeSubjectName($ws->name);
            if (str_contains($normalizedDocx, $normWs) || str_contains($normWs, $normalizedDocx)) {
                return $ws;
            }
        }
        
        // 3. Similar text (Fuzzy match)
        $bestMatch = null;
        $highestSimilarity = 0;
        foreach ($websiteSubjects as $ws) {
            $normWs = $this->normalizeSubjectName($ws->name);
            similar_text($normalizedDocx, $normWs, $percent);
            if ($percent > $highestSimilarity && $percent > 70) { 
                $highestSimilarity = $percent;
                $bestMatch = $ws;
            }
        }

        return $bestMatch;
    }

    private function matchComponentKey(string $colType, array $dbComponentKeys): ?string
    {
        foreach ($dbComponentKeys as $key) {
            $normalizedKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $key));
            
            if ($colType === 'cq') {
                if ($normalizedKey === 'cq' || str_contains($normalizedKey, 'creative') || str_contains($normalizedKey, 'written')) {
                    return $key;
                }
            }
            if ($colType === 'mcq') {
                if (str_contains($normalizedKey, 'mcq') || str_contains($normalizedKey, 'objective')) {
                    return $key;
                }
            }
            if ($colType === 'practical') {
                if (str_contains($normalizedKey, 'prac') || str_contains($normalizedKey, 'pr')) {
                    return $key;
                }
            }
            
            if ($normalizedKey === $colType) return $key;
        }
        
        return null;
    }

    private function normalizeSubjectName(string $name): string
    {
        $name = strtolower($name);
        
        $replacements = [
            '1st' => 'first', '2nd' => 'second', 'i' => 'first', 'ii' => 'second',
            'bangladesh global studies' => 'bangladesh gs',
            'bgs' => 'bangladesh gs',
            'phy' => 'physics', 'chem' => 'chemistry', 'bio' => 'biology', 'math' => 'mathematics',
            'info' => 'ict', 'information and communication technology' => 'ict',
            'agri' => 'agriculture', 'agricultural studies' => 'agriculture',
            'reli' => 'religion',
            'বাংলা' => 'bangla',
            'প্রথম' => 'first',
            'পত্র' => 'paper',
            'দ্বিতীয়' => 'second',
            'বিজ্ঞান' => 'science',
            'পদার্থবিজ্ঞান' => 'physics',
            'রসায়ন' => 'chemistry',
            'জীববিজ্ঞান' => 'biology',
            'উচ্চতর গণিত' => 'higher math',
            'গণিত' => 'mathematics'
        ];
        
        foreach ($replacements as $search => $replace) {
            if (preg_match('/^[a-z]+$/', $search)) {
                $name = preg_replace('/\b' . $search . '\b/', $replace, $name);
            } else {
                $name = str_replace($search, $replace, $name);
            }
        }

        return preg_replace('/[^a-z0-9]/', '', $name);
    }

    private function setCellText($cell, $xpath, $text)
    {
        $tNodes = $xpath->query('.//w:t', $cell);
        
        if ($tNodes->length > 0) {
            $tNodes->item(0)->nodeValue = $text;
            for ($j = $tNodes->length - 1; $j >= 1; $j--) {
                $tNodes->item($j)->parentNode->removeChild($tNodes->item($j));
            }
        } else {
            if ($text !== '') {
                $pNode = $xpath->query('.//w:p', $cell)->item(0);
                if ($pNode) {
                    $rNode = $cell->ownerDocument->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:r');
                    $tNode = $cell->ownerDocument->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:t', $text);
                    $rNode->appendChild($tNode);
                    $pNode->appendChild($rNode);
                }
            }
        }
    }

    private function makeKey($subject)
    {
        return strtolower(str_replace(' ', '_', $subject));
    }
    
    private function deleteDir($dirPath) {
        if (! is_dir($dirPath)) return;
        $files = scandir($dirPath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $fullPath = "$dirPath/$file";
                if (is_dir($fullPath)) $this->deleteDir($fullPath);
                else unlink($fullPath);
            }
        }
        rmdir($dirPath);
    }
}
