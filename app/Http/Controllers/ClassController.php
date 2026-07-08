<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::withCount(['students', 'sections'])
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $classes = $query->paginate(20)->withQueryString();

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100', Rule::unique('classes', 'name')->where('user_id', auth()->id())],
            'sort_order' => 'nullable|integer|min:0',
        ]);

        SchoolClass::create($data);

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(SchoolClass $class)
    {
        return view('classes.edit', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100', Rule::unique('classes', 'name')->ignore($class->id)->where('user_id', auth()->id())],
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $class->update($data);

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        if ($class->students()->exists()) {
            return back()->with('error', 'Cannot delete class with existing students.');
        }

        $class->delete();
        return redirect()->route('classes.index')
            ->with('success', 'Class deleted.');
    }

    public function show(SchoolClass $class)
    {
        $class->load(['sections.students']);
        return view('classes.show', compact('class'));
    }
}
