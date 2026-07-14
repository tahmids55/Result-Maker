<?php

namespace App\Http\Controllers;

use App\Models\MarksheetTemplate;
use App\Services\MarksheetGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarksheetTemplateController extends Controller
{
    public function __construct(private MarksheetGenerationService $generationService) {}

    public function index()
    {
        $templates = MarksheetTemplate::orderByDesc('is_default')->latest()->get();
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'description' => 'nullable|string',
            'template_file' => 'required|file|mimes:docx|max:10240',
        ]);

        $path = $request->file('template_file')->store('templates', 'local');

        $absolutePath = Storage::disk('local')->path($path);
        
        // Auto map placeholders first
        $autoMapper = new \App\Services\AutoMapTemplateService();
        try {
            $autoMapper->autoMap($absolutePath);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Auto-mapping failed: " . $e->getMessage());
        }

        // Extract placeholders from the uploaded .docx (after auto mapping)
        $placeholders = $this->generationService->extractPlaceholders($path);

        $template = MarksheetTemplate::create([
            'name'        => $request->name,
            'description' => $request->description,
            'file_path'   => $path,
            'placeholders'=> $placeholders,
        ]);

        return redirect()->route('templates.map', $template)
            ->with('success', 'Template uploaded. Please map the placeholders.');
    }

    public function showMapping(MarksheetTemplate $template)
    {
        $availableFields = $this->getAvailableFields();
        
        // Auto-map fields that exactly match the placeholder
        $currentMappings = $template->field_mappings ?? [];
        $hasChanges = false;
        
        // Flatten available fields to get all valid keys
        $allKeys = [];
        foreach ($availableFields as $group => $fields) {
            foreach ($fields as $key => $label) {
                $allKeys[] = $key;
            }
        }
        
        foreach ($template->placeholders ?? [] as $placeholder) {
            if (empty($currentMappings[$placeholder]) && in_array($placeholder, $allKeys)) {
                $currentMappings[$placeholder] = $placeholder;
                $hasChanges = true;
            }
        }
        
        if ($hasChanges) {
            $template->update(['field_mappings' => $currentMappings]);
        }
        
        return view('templates.map', compact('template', 'availableFields'));
    }

    public function saveMapping(Request $request, MarksheetTemplate $template)
    {
        $request->validate([
            'mappings'   => 'required|array',
            'mappings.*' => 'nullable|string',
        ]);

        $template->update(['field_mappings' => $request->mappings]);

        return redirect()->route('templates.index')
            ->with('success', 'Placeholder mappings saved.');
    }

    public function update(Request $request, MarksheetTemplate $template)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        $template->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function setDefault(MarksheetTemplate $template)
    {
        $template->setAsDefault();
        return back()->with('success', "'{$template->name}' set as default template.");
    }

    public function destroy(MarksheetTemplate $template)
    {
        Storage::disk('local')->delete($template->file_path);
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted.');
    }

    private function getAvailableFields(): array
    {
        $fields = [
            'Student Info' => [
                'st_name'         => 'Student Full Name',
                'st_photo'        => 'Student Profile Photo',
                'dob'             => 'Date of Birth',
                'gender'          => 'Student Gender',
                'roll'            => 'Roll Number',
                'reg_no'          => 'Registration Number',
                'f_name'          => 'Father\'s Name',
                'm_name'          => 'Mother\'s Name',
                'cls'             => 'Class Name',
                'sec'             => 'Section Name',
                'sess'            => 'Academic Session',
            ],
            'Exam Info' => [
                'exam_name'  => 'Exam Name',
                'exam_year'  => 'Exam Year',
            ],
            'Result Info' => [
                'tot_mks'       => 'Total Marks Obtained',
                'fl_mks'        => 'Total Full Marks',
                'pct'           => 'Percentage',
                'gpa'           => 'GPA',
                'grd'           => 'Grade',
                'div'           => 'Division',
                'status'        => 'Result (PASSED/FAILED)',
                'rank'          => 'Merit Position',
            ],
            'School Info' => [
                'sch_name'       => 'School Name',
                'sch_addr'       => 'School Address',
                'sch_ph'         => 'School Phone',
                'ftr_txt'        => 'Footer Text',
                'gen_dt'         => 'Date of Generation',
                'sch_logo'       => 'School Logo (Image)',
                'pr_sig'         => 'Principal Signature (Image)',
            ],
        ];

        // Add dynamic subjects
        $subjects = \App\Models\Subject::with('subSubjects')->get();
        foreach ($subjects as $subject) {
            $key = trim(strtolower($subject->code));
            if (!$key) {
                $key = strtolower(str_replace(' ', '_', $subject->name));
            }
            $cat = "Subject: {$subject->name}";
            $fields[$cat] = [
                "{$key}_obtained" => 'Total Marks Obtained',
                "{$key}_full"     => 'Total Full Marks',
                "{$key}_grade"    => 'Grade',
                "{$key}_gpa"      => 'GPA',
            ];
            
            if ($subject->has_sub_subjects) {
                foreach ($subject->subSubjects as $sub) {
                    $subKey = strtolower(str_replace(' ', '_', $sub->name));
                    $fields[$cat]["{$key}_{$subKey}_obtained"] = "{$sub->name} - Total Obtained";
                    $fields[$cat]["{$key}_{$subKey}_full"]     = "{$sub->name} - Total Full";
                    $fields[$cat]["{$key}_{$subKey}_grade"]    = "{$sub->name} - Grade";
                    $fields[$cat]["{$key}_{$subKey}_gpa"]      = "{$sub->name} - GPA";

                    $dbComponents = is_array($sub->exam_components) ? $sub->exam_components : [];
                    foreach (array_keys($dbComponents) as $comp) {
                        $safeComp = strtolower(str_replace(' ', '_', $comp));
                        $fields[$cat]["{$key}_{$subKey}_{$safeComp}"] = "{$sub->name} - {$comp} Marks";
                    }
                }
            } else {
                $dbComponents = is_array($subject->exam_components) ? $subject->exam_components : [];
                foreach (array_keys($dbComponents) as $comp) {
                    $safeComp = strtolower(str_replace(' ', '_', $comp));
                    $fields[$cat]["{$key}_{$safeComp}"] = $comp . ' Marks';
                }
            }
        }

        return $fields;
    }
}
