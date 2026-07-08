<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarksheetTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class EditorController extends Controller
{
    // Make sure this matches your ONLYOFFICE JWT Secret
    private $jwtSecret = 'super_secret_jwt_key_123_must_be_at_least_32_chars';

    public function edit($id)
    {
        $template = MarksheetTemplate::findOrFail($id);
        
        $downloadToken = hash_hmac('sha256', $template->id, $this->jwtSecret);
        
        // Use standard app url, assuming ONLYOFFICE container can reach this host.
        // If ONLYOFFICE is inside Docker, it might need to hit a specific IP, but we'll use standard config for now.
        // E.g., request()->getSchemeAndHttpHost() or env('APP_URL')
        // We'll use a hardcoded fallback to host.docker.internal if APP_URL is localhost for typical dev setups
        $publicHost = request()->getSchemeAndHttpHost();
        if (app()->environment('production') && str_starts_with(env('APP_URL'), 'https://')) {
            $publicHost = str_replace('http://', 'https://', $publicHost);
        }

        // Use public host for all ONLYOFFICE communication
        // Cloudflare handles these requests fine.
        $downloadUrl = $publicHost . route('onlyoffice.download', ['id' => $template->id, 'token' => $downloadToken], false);
        $callbackUrl = $publicHost . route('onlyoffice.callback', [], false);

        $documentKey = $template->id . '_' . filemtime(Storage::disk('local')->path($template->file_path));

        $config = [
            'document' => [
                'fileType' => 'docx',
                'key' => $documentKey,
                'title' => $template->name . '.docx',
                'url' => $downloadUrl,
            ],
            'documentType' => 'word',
            'editorConfig' => [
                'callbackUrl' => $callbackUrl,
                'user' => [
                    'id' => (string) (auth()->id() ?? '1'),
                    'name' => auth()->user() ? auth()->user()->name : 'Admin'
                ],
                'customization' => [
                    'forcesave' => true,
                ],
                'plugins' => [
                    'autostart' => [
                        'asc.{E9C9F9A5-C4BD-4DF8-97F1-79A595B40974}' // Our plugin ID
                    ],
                    'pluginsData' => [
                        // The URL to the plugin configuration JSON
                        $publicHost . '/onlyoffice-plugin/panel/config.json?v=' . time()
                    ]
                ]
            ]
        ];

        $token = JWT::encode($config, $this->jwtSecret, 'HS256');
        $config['token'] = $token;

        return view('editor', [
            'config' => $config,
            'template' => $template
        ]);
    }

    public function download($id, $token)
    {
        $expectedToken = hash_hmac('sha256', $id, $this->jwtSecret);
        if (!hash_equals($expectedToken, $token)) {
            abort(403, 'Invalid token');
        }

        $template = MarksheetTemplate::findOrFail($id);
        $path = Storage::disk('local')->path($template->file_path);

        return response()->file($path);
    }

    public function callback(Request $request)
    {
        Log::info('ONLYOFFICE Callback received', $request->all());

        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return response()->json(['error' => 1, 'message' => 'Missing Authorization header']);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        try {
            $payload = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
        } catch (\Exception $e) {
            Log::error('ONLYOFFICE JWT Invalid: ' . $e->getMessage());
            return response()->json(['error' => 1, 'message' => 'Invalid token']);
        }

        $status = $request->input('status');
        if ($status == 2 || $status == 3 || $status == 6) {
            $downloadUri = $request->input('url');
            if ($downloadUri) {
                $key = $request->input('key');
                $parts = explode('_', $key);
                $templateId = $parts[0];

                $template = MarksheetTemplate::find($templateId);
                if ($template) {
                    $newContent = file_get_contents($downloadUri);
                    if ($newContent) {
                        Storage::disk('local')->put($template->file_path, $newContent);
                        Log::info("Template {$templateId} updated via ONLYOFFICE callback");
                        
                        // Update placeholders array after save
                        $service = app(\App\Services\MarksheetGenerationService::class);
                        $placeholders = $service->extractPlaceholders($template->file_path);
                        $template->update(['placeholders' => $placeholders]);
                    }
                }
            }
        }

        return response()->json(['error' => 0]);
    }

    public function placeholders()
    {
        // Define placeholders structurally for ONLYOFFICE
        $fields = [];
        
        $baseFields = [
            'Student Info' => [
                'student_name'    => 'Student Full Name',
                'roll'            => 'Roll Number',
                'registration_no' => 'Registration Number',
                'father_name'     => 'Father\'s Name',
                'mother_name'     => 'Mother\'s Name',
                'class_name'      => 'Class Name',
                'section_name'    => 'Section Name',
                'session'         => 'Academic Session',
            ],
            'Exam Info' => [
                'exam_name'  => 'Exam Name',
                'exam_year'  => 'Exam Year',
            ],
            'Result Info' => [
                'total_marks'   => 'Total Marks Obtained',
                'full_marks'    => 'Total Full Marks',
                'percentage'    => 'Percentage',
                'gpa'           => 'GPA',
                'grade'         => 'Grade',
                'division'      => 'Division',
                'result_status' => 'Result (PASSED/FAILED)',
                'rank'          => 'Merit Position',
            ],
            'School Info' => [
                'school_name'    => 'School Name',
                'school_address' => 'School Address',
                'school_phone'   => 'School Phone',
                'footer_text'    => 'Footer Text',
                'generated_date' => 'Date of Generation',
                'school_logo'    => 'School Logo (Image)',
                'principal_signature' => 'Principal Signature (Image)',
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
            $baseFields[$cat] = [
                "{$key}_obtained" => 'Total Marks Obtained',
                "{$key}_full"     => 'Total Full Marks',
                "{$key}_grade"    => 'Grade',
                "{$key}_gpa"      => 'GPA',
            ];
            
            if ($subject->has_sub_subjects) {
                foreach ($subject->subSubjects as $sub) {
                    $subKey = strtolower(str_replace(' ', '_', $sub->name));
                    $baseFields[$cat]["{$key}_{$subKey}_obtained"] = "{$sub->name} - Total Obtained";
                    $baseFields[$cat]["{$key}_{$subKey}_full"]     = "{$sub->name} - Total Full";
                    $baseFields[$cat]["{$key}_{$subKey}_grade"]    = "{$sub->name} - Grade";
                    $baseFields[$cat]["{$key}_{$subKey}_gpa"]      = "{$sub->name} - GPA";

                    $dbComponents = is_array($sub->exam_components) ? $sub->exam_components : [];
                    foreach (array_keys($dbComponents) as $comp) {
                        $safeComp = strtolower(str_replace(' ', '_', $comp));
                        $baseFields[$cat]["{$key}_{$subKey}_{$safeComp}"] = "{$sub->name} - {$comp} Marks";
                    }
                }
            } else {
                $dbComponents = is_array($subject->exam_components) ? $subject->exam_components : [];
                foreach (array_keys($dbComponents) as $comp) {
                    $safeComp = strtolower(str_replace(' ', '_', $comp));
                    $baseFields[$cat]["{$key}_{$safeComp}"] = $comp . ' Marks';
                }
            }
        }

        foreach ($baseFields as $category => $items) {
            foreach ($items as $key => $label) {
                $fields[] = [
                    'category' => $category,
                    'label' => $label,
                    'placeholder' => '${' . $key . '}',
                    'description' => 'System placeholder for ' . $label
                ];
            }
        }

        return response()->json($fields);
    }
}
