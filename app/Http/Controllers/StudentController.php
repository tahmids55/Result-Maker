<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'section'])
            ->orderBy('class_id')
            ->orderBy('section_id')
            ->orderBy('roll');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('roll', 'like', "%{$request->search}%")
                  ->orWhere('registration_no', 'like', "%{$request->search}%");
            });
        }

        $perPage = $request->input('per_page', 30);
        $students = $query->paginate($perPage)->withQueryString();
        $classes  = SchoolClass::orderBy('sort_order')->get();
        $sections = $request->class_id
            ? Section::where('class_id', $request->class_id)->get()
            : collect();

        return view('students.index', compact('students', 'classes', 'sections'));
    }

    public function create()
    {
        $classes  = SchoolClass::orderBy('sort_order')->get();
        $sections = collect();
        return view('students.create', compact('classes', 'sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:200',
            'gender'          => 'nullable|string|in:Male,Female',
            'roll'            => 'required|integer|min:1',
            'registration_no' => ['nullable', 'string', 'max:50', Rule::unique('students', 'registration_no')->where('user_id', auth()->id())],
            'father_name'     => 'nullable|string|max:200',
            'mother_name'     => 'nullable|string|max:200',
            'class_id'        => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id'      => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'session'         => 'nullable|string|max:20',
            'dob'             => 'nullable|date',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'profile_photo'   => 'nullable|image|max:2048',
        ]);

        $data['roll'] = (int) $data['roll'];

        // Unique roll per class+section
        $exists = Student::where('roll', $data['roll'])
            ->where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['roll' => 'Roll number already taken in this class/section.'])->withInput();
        }

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('students/photos', 'public');
        }

        Student::create($data);

        return redirect()->route('students.index')
            ->with('success', 'Student added successfully.');
    }

    public function edit(Student $student)
    {
        $classes  = SchoolClass::orderBy('sort_order')->get();
        $sections = Section::where('class_id', $student->class_id)->get();
        return view('students.edit', compact('student', 'classes', 'sections'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:200',
            'gender'          => 'nullable|string|in:Male,Female',
            'roll'            => 'required|integer|min:1',
            'registration_no' => ['nullable', 'string', 'max:50', Rule::unique('students', 'registration_no')->ignore($student->id)->where('user_id', auth()->id())],
            'father_name'     => 'nullable|string|max:200',
            'mother_name'     => 'nullable|string|max:200',
            'class_id'        => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id'      => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'session'         => 'nullable|string|max:20',
            'dob'             => 'nullable|date',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string',
            'profile_photo'   => 'nullable|image|max:2048',
        ]);

        $data['roll'] = (int) $data['roll'];

        // Unique roll per class+section (excluding current student)
        $exists = Student::where('roll', $data['roll'])
            ->where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->where('id', '!=', $student->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['roll' => 'Roll number already taken in this class/section.'])->withInput();
        }

        if ($request->hasFile('profile_photo')) {
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('students/photos', 'public');
        }

        $student->update($data);

        return redirect()->route('students.index')
            ->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        if ($student->profile_photo) {
            Storage::disk('public')->delete($student->profile_photo);
        }
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }

    public function show(Student $student)
    {
        $student->load(['schoolClass', 'section', 'results.exam']);
        return view('students.show', compact('student'));
    }

    public function bulkImport(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:5120']);

        $lines = array_map('str_getcsv', file($request->file('csv_file')->getPathname()));
        $count = 0;
        $errors = [];

        // Expected CSV: name, roll, father_name, mother_name, class_name, section_name, session
        foreach (array_slice($lines, 1) as $i => $row) {
            if (count($row) < 6) continue;
            [$name, $roll, $fatherName, $motherName, $className, $sectionName] = $row;
            $session = $row[6] ?? null;

            $class   = SchoolClass::where('name', trim($className))->first();
            $section = $class ? Section::where('class_id', $class->id)->where('name', trim($sectionName))->first() : null;

            if (!$class || !$section) {
                $errors[] = "Row " . ($i + 2) . ": Class '{$className}' or Section '{$sectionName}' not found.";
                continue;
            }

            $exists = Student::where('roll', (int) $roll)
                ->where('class_id', $class->id)
                ->where('section_id', $section->id)
                ->exists();

            if ($exists) {
                $errors[] = "Row " . ($i + 2) . ": Roll {$roll} already exists in {$className}-{$sectionName}.";
                continue;
            }

            Student::create([
                'name'        => trim($name),
                'roll'        => (int) $roll,
                'father_name' => trim($fatherName),
                'mother_name' => trim($motherName),
                'class_id'    => $class->id,
                'section_id'  => $section->id,
                'session'     => $session ? trim($session) : null,
            ]);
            $count++;
        }

        $message = "{$count} students imported.";
        if ($errors) {
            $message .= ' ' . count($errors) . ' rows skipped.';
        }

        return redirect()->route('students.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    public function getSectionsByClass(Request $request)
    {
        $sections = Section::where('class_id', $request->class_id)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($sections);
    }
}
