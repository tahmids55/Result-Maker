<?php

namespace App\Http\Controllers;

use App\Models\GradeConfig;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $school  = School::first();
        $grades  = GradeConfig::orderByDesc('min_percentage')->get();
        return view('settings.index', compact('school', 'grades'));
    }

    public function updateSchool(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'address'     => 'nullable|string',
            'phone'       => 'nullable|string|max:30',
            'email'       => 'nullable|email|max:100',
            'footer_text' => 'nullable|string',
            'date_format' => 'required|in:d/m/Y,m/d/Y,Y-m-d',
            'gpa_scale'   => 'required|in:5.0,4.0',
            'logo'             => 'nullable|image|max:2048',
            'signature'        => 'nullable|image|max:2048',
            'sms_api_key'      => 'nullable|string|max:255',
            'whatsapp_api_key' => 'nullable|string|max:255',
        ]);

        $school = School::firstOrNew();

        if ($request->hasFile('logo')) {
            if ($school->logo) Storage::disk('public')->delete($school->logo);
            $data['logo'] = $request->file('logo')->store('school', 'public');
        }
        if ($request->hasFile('signature')) {
            if ($school->signature) Storage::disk('public')->delete($school->signature);
            $data['signature'] = $request->file('signature')->store('school', 'public');
        }

        $school->fill($data)->save();

        return back()->with('success', 'School settings saved.');
    }

    public function updateGrades(Request $request)
    {
        $request->validate([
            'grades'                => 'required|array|min:1',
            'grades.*.grade'        => 'required|string|max:10',
            'grades.*.gpa'          => 'required|numeric|min:0|max:5',
            'grades.*.min_percentage' => 'required|numeric|min:0|max:100',
            'grades.*.max_percentage' => 'required|numeric|min:0|max:100',
            'grades.*.label'        => 'nullable|string|max:100',
        ]);

        GradeConfig::where('user_id', auth()->id())->delete();

        foreach ($request->grades as $i => $g) {
            GradeConfig::create([
                'grade'          => $g['grade'],
                'gpa'            => $g['gpa'],
                'min_percentage' => $g['min_percentage'],
                'max_percentage' => $g['max_percentage'],
                'label'          => $g['label'] ?? null,
                'sort_order'     => $i,
            ]);
        }

        return back()->with('success', 'Grading system updated.');
    }

    public function backup()
    {
        try {
            Artisan::call('markscraft:backup');
            $output   = Artisan::output();
            $filename = trim(explode(':', $output)[1] ?? 'backup.sql');
            return response()->download(storage_path("app/backups/{$filename}"));
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate(['backup_file' => 'required|file|mimes:sql|max:102400']);

        try {
            $path = $request->file('backup_file')->store('backups', 'local');
            Artisan::call('markscraft:restore', ['file' => $path, '--force' => true]);
            return back()->with('success', 'Database restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
}
