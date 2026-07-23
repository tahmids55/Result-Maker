<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['schoolClass', 'section'])->orderBy('class_id')->orderBy('sort_order');

        if ($request->filled('class_id'))   $query->where('class_id', $request->class_id);
        if ($request->filled('section_id')) $query->where('section_id', $request->section_id);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $subjects = $query->paginate(20)->withQueryString();
        $classes  = SchoolClass::orderBy('sort_order')->get();
        $sections = $request->class_id ? Section::where('class_id', $request->class_id)->get() : collect();

        return view('subjects.index', compact('subjects', 'classes', 'sections'));
    }

    public function create()
    {
        $classes  = SchoolClass::orderBy('sort_order')->get();
        $sections = collect();
        return view('subjects.create', compact('classes', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:200',
            'code'              => 'nullable|string|max:20',
            'class_id'          => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id'        => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'has_sub_subjects'  => 'boolean',
            'components'        => 'required_unless:has_sub_subjects,1|array',
            'components.*.name' => 'required_with:components|string|max:50',
            'components.*.full' => 'required_with:components|numeric|min:1',
            'components.*.pass' => 'required_with:components|numeric|min:1',
            'sub_subjects'      => 'required_if:has_sub_subjects,1|array|min:1',
            'sub_subjects.*.name' => 'required_with:sub_subjects|string|max:100',
            'sub_subjects.*.components' => 'required_with:sub_subjects|array|min:1',
            'is_optional'       => 'boolean',
            'has_sub_subjects'  => 'boolean',
            'accumulated_pass_marks' => 'boolean',
            'sort_order'        => 'nullable|integer',
        ]);

        $hasSubSubjects = $request->boolean('has_sub_subjects');

        $components = [];
        if (!$hasSubSubjects && $request->components) {
            foreach ($request->components as $comp) {
                $key = strtolower(trim($comp['name']));
                $components[$key] = [
                    'full' => (float) $comp['full'],
                    'pass' => (float) $comp['pass'],
                ];
            }
        }

        $subject = Subject::create([
            'name'            => $request->name,
            'code'            => $request->code,
            'class_id'        => $request->class_id,
            'section_id'      => $request->section_id,
            'has_sub_subjects'=> $hasSubSubjects,
            'exam_components' => $hasSubSubjects ? [] : $components,
            'is_optional'     => $request->boolean('is_optional'),
            'accumulated_pass_marks' => $request->boolean('accumulated_pass_marks'),
            'sort_order'      => $request->sort_order ?? 0,
        ]);

        if ($hasSubSubjects && $request->sub_subjects) {
            foreach ($request->sub_subjects as $index => $subReq) {
                $subComponents = [];
                foreach ($subReq['components'] as $comp) {
                    $key = strtolower(trim($comp['name']));
                    $subComponents[$key] = [
                        'full' => (float) $comp['full'],
                        'pass' => (float) $comp['pass'],
                    ];
                }
                $subject->subSubjects()->create([
                    'name' => $subReq['name'],
                    'exam_components' => $subComponents,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('subjects.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject)
    {
        $subject->load('subSubjects');
        $classes  = SchoolClass::orderBy('sort_order')->get();
        $sections = Section::where('class_id', $subject->class_id)->get();
        return view('subjects.edit', compact('subject', 'classes', 'sections'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name'              => 'required|string|max:200',
            'code'              => 'nullable|string|max:20',
            'class_id'          => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'section_id'        => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'has_sub_subjects'  => 'boolean',
            'components'        => 'required_unless:has_sub_subjects,1|array',
            'components.*.name' => 'required_with:components|string|max:50',
            'components.*.full' => 'required_with:components|numeric|min:1',
            'components.*.pass' => 'required_with:components|numeric|min:1',
            'sub_subjects'      => 'required_if:has_sub_subjects,1|array|min:1',
            'sub_subjects.*.name' => 'required_with:sub_subjects|string|max:100',
            'sub_subjects.*.components' => 'required_with:sub_subjects|array|min:1',
            'is_optional'       => 'boolean',
            'accumulated_pass_marks' => 'boolean',
        ]);

        $hasSubSubjects = $request->boolean('has_sub_subjects');

        $components = [];
        if (!$hasSubSubjects && $request->components) {
            foreach ($request->components as $comp) {
                $key = strtolower(trim($comp['name']));
                $components[$key] = ['full' => (float) $comp['full'], 'pass' => (float) $comp['pass']];
            }
        }

        $subject->update([
            'name'            => $request->name,
            'code'            => $request->code,
            'class_id'        => $request->class_id,
            'section_id'      => $request->section_id,
            'has_sub_subjects'=> $hasSubSubjects,
            'exam_components' => $hasSubSubjects ? [] : $components,
            'is_optional'     => $request->boolean('is_optional'),
            'accumulated_pass_marks' => $request->boolean('accumulated_pass_marks'),
            'sort_order'      => $request->sort_order ?? 0,
        ]);

        $subject->subSubjects()->delete(); // Recreate them for simplicity
        
        if ($hasSubSubjects && $request->sub_subjects) {
            foreach ($request->sub_subjects as $index => $subReq) {
                $subComponents = [];
                foreach ($subReq['components'] as $comp) {
                    $key = strtolower(trim($comp['name']));
                    $subComponents[$key] = [
                        'full' => (float) $comp['full'],
                        'pass' => (float) $comp['pass'],
                    ];
                }
                $subject->subSubjects()->create([
                    'name' => $subReq['name'],
                    'exam_components' => $subComponents,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->marks()->exists()) {
            return back()->with('error', 'Cannot delete subject with existing marks.');
        }
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Subject deleted.');
    }

    public function copy(Subject $subject)
    {
        $newSubject = $subject->replicate();
        $newSubject->name = $newSubject->name . ' (Copy)';
        $newSubject->save();

        return redirect()->route('subjects.edit', $newSubject)
                         ->with('success', 'Subject duplicated. You can now edit its details.');
    }

    /**
     * Copy all subjects from one class to another.
     */
    public function copyToClass(Request $request)
    {
        $request->validate([
            'from_class_id'   => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'from_section_id' => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
            'to_class_id'     => ['required', \Illuminate\Validation\Rule::exists('classes', 'id')->where('user_id', auth()->id())],
            'to_section_id'   => ['required', \Illuminate\Validation\Rule::exists('sections', 'id')->where('user_id', auth()->id())],
        ]);

        $subjects = Subject::where('class_id', $request->from_class_id)
            ->where('section_id', $request->from_section_id)
            ->get();

        $count = 0;
        foreach ($subjects as $subject) {
            $exists = Subject::where('name', $subject->name)
                ->where('class_id', $request->to_class_id)
                ->where('section_id', $request->to_section_id)
                ->exists();

            if (!$exists) {
                Subject::create([
                    'name'            => $subject->name,
                    'code'            => $subject->code,
                    'class_id'        => $request->to_class_id,
                    'section_id'      => $request->to_section_id,
                    'exam_components' => $subject->exam_components,
                    'is_optional'     => $subject->is_optional,
                    'accumulated_pass_marks' => $subject->accumulated_pass_marks,
                    'sort_order'      => $subject->sort_order,
                ]);
                $count++;
            }
        }

        return redirect()->route('subjects.index')
            ->with('success', "{$count} subjects copied successfully.");
    }
}
