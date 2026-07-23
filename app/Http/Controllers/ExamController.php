<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::orderByDesc('year')->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('year', 'like', "%{$request->search}%");
        }

        $exams = $query->paginate(15)->withQueryString();
        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        return view('exams.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:200',
            'year'       => 'required|integer|min:2000|max:2100',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'is_active'  => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        Exam::create($data);
        return redirect()->route('exams.index')->with('success', 'Exam created.');
    }

    public function edit(Exam $exam)
    {
        return view('exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:200',
            'year'       => 'required|integer|min:2000|max:2100',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'is_active'  => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');

        $exam->update($data);
        return redirect()->route('exams.index')->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        if ($exam->marks()->exists()) {
            return back()->with('error', 'Cannot delete exam with existing marks.');
        }
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'Exam deleted.');
    }

    public function toggleActive(Exam $exam)
    {
        $exam->update(['is_active' => !$exam->is_active]);
        $status = $exam->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Exam {$status}.");
    }
}
