<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Section::with('schoolClass')
            ->withCount('students')
            ->orderBy('class_id')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhereHas('schoolClass', function ($q) use ($request) {
                      $q->where('name', 'like', "%{$request->search}%");
                  });
        }

        $sections = $query->paginate(20)->withQueryString();

        return view('sections.index', compact('sections'));
    }

    public function create()
    {
        $classes = SchoolClass::orderBy('sort_order')->orderBy('name')->get();
        return view('sections.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class_id' => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'name'     => [
                'required', 'string', 'max:100',
                Rule::unique('sections')->where('class_id', $request->class_id)->where('user_id', auth()->id()),
            ],
        ]);

        Section::create($data);

        return redirect()->route('sections.index')
            ->with('success', 'Section created successfully.');
    }

    public function edit(Section $section)
    {
        $classes = SchoolClass::orderBy('sort_order')->orderBy('name')->get();
        return view('sections.edit', compact('section', 'classes'));
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'class_id' => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'name'     => [
                'required', 'string', 'max:100',
                Rule::unique('sections')->where('class_id', $request->class_id)->ignore($section->id)->where('user_id', auth()->id()),
            ],
        ]);

        $section->update($data);

        return redirect()->route('sections.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        if ($section->students()->exists()) {
            return back()->with('error', 'Cannot delete section with existing students.');
        }

        $section->delete();
        return redirect()->route('sections.index')->with('success', 'Section deleted.');
    }

    public function bulkImport(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        $file  = $request->file('csv_file');
        $lines = array_map('str_getcsv', file($file->getPathname()));
        $count = 0;

        foreach (array_slice($lines, 1) as $row) {
            if (count($row) < 2) continue;
            [$className, $sectionName] = $row;

            $class = SchoolClass::firstOrCreate(['name' => trim($className)]);
            Section::firstOrCreate([
                'class_id' => $class->id,
                'name'     => trim($sectionName),
            ]);
            $count++;
        }

        return redirect()->route('sections.index')
            ->with('success', "{$count} sections imported successfully.");
    }
}
