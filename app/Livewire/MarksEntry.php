<?php

namespace App\Livewire;

use App\DTOs\MarkEntryDTO;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Repositories\MarkRepository;
use App\Services\ResultCalculationService;
use Livewire\Component;

class MarksEntry extends Component
{
    // Selectors
    #[\Livewire\Attributes\Url]
    public ?int $classId   = null;
    #[\Livewire\Attributes\Url]
    public ?int $sectionId = null;
    #[\Livewire\Attributes\Url]
    public ?int $examId    = null;
    #[\Livewire\Attributes\Url]
    public ?int $subjectId = null;

    // Data
    public array $students = [];
    public array $subjects = [];
    public array $marks    = [];   // [student_id][subject_id][sub_subject_id_or_0][component] = obtained

    // State
    public bool  $loaded    = false;
    public bool  $saving    = false;
    public bool  $autoSaveEnabled = false;
    public array $errors_   = [];

    // Computed (reactive) - Handled entirely client-side by AlpineJS now

    public function mount(): void
    {
        $this->autoSaveEnabled = (bool) (\App\Models\School::getSettings()?->auto_save_marks ?? false);

        if ($this->classId && $this->sectionId && $this->examId) {
            $this->loadMarks();
        }
    }

    public function updatedClassId(): void
    {
        $this->sectionId = null;
        $this->subjectId = null;
        $this->resetGrid();
    }

    public function updatedSectionId(): void
    {
        $this->subjectId = null;
        $this->resetGrid();
    }

    public function updatedExamId(): void
    {
        $this->resetGrid();
    }

    public function updatedSubjectId(): void
    {
        $this->resetGrid();
    }

    public function loadMarks(): void
    {
        if (!$this->classId || !$this->sectionId || !$this->examId) {
            session()->flash('error', 'Please select class, section, and exam.');
            return;
        }

        $students = Student::where('class_id', $this->classId)
            ->where('section_id', $this->sectionId)
            ->orderBy('roll')
            ->get();

        $subjectsQuery = Subject::with('subSubjects')
            ->where('class_id', $this->classId)
            ->where('section_id', $this->sectionId)
            ->orderBy('sort_order');

        // Teachers can only enter marks for their assigned subjects
        if (auth()->user()->isTeacher()) {
            $assignedIds = auth()->user()->assignedSubjects()->pluck('subjects.id');
            $subjectsQuery->whereIn('id', $assignedIds);
        }

        if ($this->subjectId) {
            $subjectsQuery->where('id', $this->subjectId);
        }

        $subjects = $subjectsQuery->get();

        // Pre-load ALL marks in a single query instead of N+1
        $existingMarks = Mark::where('exam_id', $this->examId)
            ->whereIn('student_id', $students->pluck('id'))
            ->whereIn('subject_id', $subjects->pluck('id'))
            ->get()
            ->groupBy(function ($mark) {
                return $mark->student_id . '_' . $mark->subject_id . '_' . ($mark->sub_subject_id ?? 0) . '_' . $mark->component;
            });

        // Build $marks structure from pre-loaded data
        $marks = [];
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                if ($subject->has_sub_subjects) {
                    foreach ($subject->subSubjects as $sub) {
                        foreach (array_keys($sub->exam_components ?? []) as $component) {
                            $key = $student->id . '_' . $subject->id . '_' . $sub->id . '_' . $component;
                            $mark = $existingMarks->get($key)?->first();
                            $marks[$student->id][$subject->id][$sub->id][$component] = $mark ? (string) $mark->obtained_marks : '';
                        }
                    }
                } else {
                    foreach (array_keys($subject->exam_components ?? []) as $component) {
                        $key = $student->id . '_' . $subject->id . '_0_' . $component;
                        $mark = $existingMarks->get($key)?->first();
                        $marks[$student->id][$subject->id][0][$component] = $mark ? (string) $mark->obtained_marks : '';
                    }
                }
            }
        }

        $this->students = $students->toArray();
        $this->subjects = $subjects->map(function ($s) {
            return [
                'id'               => $s->id,
                'name'             => $s->name,
                'code'             => $s->code,
                'has_sub_subjects' => $s->has_sub_subjects,
                'is_optional'      => $s->is_optional ?? false,
                'full_marks'       => $s->full_marks,
                'pass_marks'       => $s->pass_marks,
                'is_individual_pass' => $s->is_individual_pass,
                'exam_components'  => $s->exam_components,
                'sub_subjects'     => $s->subSubjects->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'exam_components' => $sub->exam_components
                    ];
                })->toArray(),
            ];
        })->toArray();
        $this->marks  = $marks;
        $this->loaded = true;

        // Dispatch hydration event for the Alpine.js client state engine
        $this->dispatch('marks-hydrate', [
            'marks'    => $marks,
            'students' => $this->students,
            'subjects' => $this->subjects,
            'gradeMap' => $this->getGradeMap(),
            'wireId'   => $this->getId(),
        ]);
    }



    /**
     * Batch save endpoint for the Alpine.js client-side state engine.
     * Receives an array of dirty cells and persists them via single UPSERT.
     */
    public function saveBatch(array $batch): array
    {
        if (!$this->loaded || !$this->examId) {
            return ['saved' => 0, 'errors' => ['Not loaded']];
        }

        $subjectCache = collect($this->subjects)->keyBy('id');
        $dtos = [];
        $errors = [];

        foreach ($batch as $item) {
            $parts = explode('_', $item['key'] ?? '', 4);
            if (count($parts) < 4) {
                $errors[] = "Invalid key: {$item['key']}";
                continue;
            }

            [$sid, $subjId, $subId, $comp] = $parts;
            $subject = $subjectCache->get((int) $subjId);
            if (!$subject) continue;

            $config = $this->resolveComponentConfig($subject, (int) $subId, $comp);
            if (!$config) continue;

            if (isset($item['value']) && $item['value'] === '') {
                // The user cleared the input, so we should delete the mark
                $marksToDelete = Mark::where('student_id', (int) $sid)
                    ->where('subject_id', (int) $subjId)
                    ->where('sub_subject_id', (int) $subId ?: null)
                    ->where('exam_id', $this->examId)
                    ->where('component', $comp)
                    ->get();
                
                foreach ($marksToDelete as $mark) {
                    $mark->delete();
                }
                continue;
            }

            $obtained = (float) ($item['value'] ?? 0);
            if ($obtained > $config['full']) {
                $errors[] = "Exceeds max for {$comp}";
                continue;
            }

            $dtos[] = new MarkEntryDTO(
                studentId: (int) $sid,
                subjectId: (int) $subjId,
                subSubjectId: (int) $subId ?: null,
                examId: $this->examId,
                component: $comp,
                obtained: $obtained,
                full: (float) $config['full'],
                pass: (float) $config['pass'],
            );
        }

        $saved = app(MarkRepository::class)->upsertBatch($dtos);

        return ['saved' => $saved, 'errors' => $errors];
    }

    /**
     * Resolve the exam component config for a given subject/sub-subject/component combo.
     */
    private function resolveComponentConfig(array $subject, int $subId, string $comp): ?array
    {
        if ($subject['has_sub_subjects'] && $subId > 0) {
            foreach ($subject['sub_subjects'] as $sub) {
                if ($sub['id'] === $subId && isset($sub['exam_components'][$comp])) {
                    return $sub['exam_components'][$comp];
                }
            }
        } elseif (isset($subject['exam_components'][$comp])) {
            return $subject['exam_components'][$comp];
        }
        return null;
    }



    private function getGradeMap(): array
    {
        $configs = \App\Models\GradeConfig::orderByDesc('min_percentage')->get();

        if ($configs->isNotEmpty()) {
            return $configs->map(fn($c) => [
                'grade' => $c->grade,
                'gpa'   => $c->gpa,
                'min'   => $c->min_percentage,
                'max'   => $c->max_percentage,
            ])->toArray();
        }

        // Fallback defaults if no DB grades exist
        return [
            ['grade' => 'A+', 'gpa' => 5.00, 'min' => 80,  'max' => 100],
            ['grade' => 'A',  'gpa' => 4.00, 'min' => 70,  'max' => 79.99],
            ['grade' => 'A-', 'gpa' => 3.50, 'min' => 60,  'max' => 69.99],
            ['grade' => 'B',  'gpa' => 3.00, 'min' => 50,  'max' => 59.99],
            ['grade' => 'C',  'gpa' => 2.00, 'min' => 40,  'max' => 49.99],
            ['grade' => 'D',  'gpa' => 1.00, 'min' => 33,  'max' => 39.99],
            ['grade' => 'F',  'gpa' => 0.00, 'min' => 0,   'max' => 32.99],
        ];
    }

    private function resetGrid(): void
    {
        $this->students      = [];
        $this->subjects      = [];
        $this->marks         = [];
        $this->loaded        = false;
        $this->rowTotals     = [];
        $this->rowPercentages= [];
        $this->rowGpas       = [];
        $this->rowGrades     = [];
        $this->rowPassed     = [];
    }

    public function render()
    {
        $classes  = SchoolClass::orderBy('sort_order')->orderBy('name')->get();
        $sections = $this->classId
            ? Section::where('class_id', $this->classId)->orderBy('name')->get()
            : collect();
        $exams    = Exam::orderByDesc('year')->orderBy('name')->get();

        $availableSubjects = collect();
        if ($this->classId && $this->sectionId) {
            $query = Subject::where('class_id', $this->classId)
                     ->where('section_id', $this->sectionId)
                     ->orderBy('sort_order');

            // Teachers can only see their assigned subjects
            if (auth()->user()->isTeacher()) {
                $assignedIds = auth()->user()->assignedSubjects()->pluck('subjects.id');
                $query->whereIn('id', $assignedIds);
            }

            $availableSubjects = $query->get();
        }

        return view('livewire.marks-entry', compact('classes', 'sections', 'exams', 'availableSubjects'));
    }
}
