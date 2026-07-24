<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::where('admin_id', auth()->id())
            ->where('role', 'teacher')
            ->with('assignedSubjects.schoolClass', 'assignedSubjects.section')
            ->orderBy('name')
            ->paginate(20);

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        $subjects = Subject::with('schoolClass', 'section')
            ->orderBy('class_id')
            ->orderBy('sort_order')
            ->get();

        return view('teachers.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'username'    => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')],
            'password'    => 'required|string|min:6|confirmed',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $teacher = User::create([
            'name'     => $request->name,
            'username' => strtolower($request->username),
            'email'    => strtolower($request->username) . '@teacher.local', // placeholder email
            'password' => $request->password,
            'role'     => 'teacher',
            'admin_id' => auth()->id(),
        ]);

        if ($request->subject_ids) {
            $teacher->assignedSubjects()->sync($request->subject_ids);
        }

        return redirect()->route('teachers.index')->with('success', 'Teacher created successfully.');
    }

    public function edit(User $teacher)
    {
        // Ensure this teacher belongs to current admin
        if ($teacher->admin_id !== auth()->id() || !$teacher->isTeacher()) {
            abort(403);
        }

        $teacher->load('assignedSubjects');

        $subjects = Subject::with('schoolClass', 'section')
            ->orderBy('class_id')
            ->orderBy('sort_order')
            ->get();

        return view('teachers.edit', compact('teacher', 'subjects'));
    }

    public function update(Request $request, User $teacher)
    {
        if ($teacher->admin_id !== auth()->id() || !$teacher->isTeacher()) {
            abort(403);
        }

        $request->validate([
            'name'        => 'required|string|max:200',
            'username'    => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($teacher->id)],
            'password'    => 'nullable|string|min:6|confirmed',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $teacher->update([
            'name'     => $request->name,
            'username' => strtolower($request->username),
        ]);

        if ($request->filled('password')) {
            $teacher->update(['password' => Hash::make($request->password)]);
        }

        $teacher->assignedSubjects()->sync($request->subject_ids ?? []);

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(User $teacher)
    {
        if ($teacher->admin_id !== auth()->id() || !$teacher->isTeacher()) {
            abort(403);
        }

        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Teacher deleted.');
    }
}
