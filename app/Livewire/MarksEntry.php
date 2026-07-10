<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Services\ResultCalculationService;
use Livewire\Component;

class MarksEntry extends Component
{
    // Selectors
    public ?int $classId   = null;
    public ?int $sectionId = null;
    public ?int $examId    = null;
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

    // Computed (reactive)
    public array $rowTotals        = [];  // [student_id] => total
    public array $rowPercentages   = [];  // [student_id] => pct
    public array $rowGpas          = [];  // [student_id] => gpa
    public array $rowGrades        = [];  // [student_id] => grade
    public array $rowPassed        = [];  // [student_id] => bool

    public function mount(): void
    {
        $this->autoSaveEnabled = (bool) (\App\Models\School::getSettings()?->auto_save_marks ?? false);
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

        $this->recalculateAll();
    }

    public function updatedMarks(): void
    {
        $this->recalculateAll();
    }

    public function saveMarks(bool $silent = false): void
    {
        if (!$this->loaded) return;

        $this->saving   = true;
        $this->errors_  = [];

        $saved = 0;
        foreach ($this->students as $student) {
            foreach ($this->subjects as $subject) {
                if ($subject['has_sub_subjects']) {
                    foreach ($subject['sub_subjects'] as $sub) {
                        foreach ($sub['exam_components'] as $componentName => $config) {
                            $obtained = $this->marks[$student['id']][$subject['id']][$sub['id']][$componentName] ?? null;
                            if ($obtained === null || $obtained === '') continue;

                            $obtained = (float) $obtained;
                            $full     = (float) ($config['full'] ?? 0);

                            if ($obtained > $full) {
                                $this->errors_[] = "Student {$student['name']}: {$sub['name']} {$componentName} exceeds max ({$full}).";
                                continue;
                            }

                            Mark::updateOrCreate(
                                [
                                    'student_id'     => $student['id'],
                                    'subject_id'     => $subject['id'],
                                    'sub_subject_id' => $sub['id'],
                                    'exam_id'        => $this->examId,
                                    'component'      => $componentName,
                                ],
                                [
                                    'obtained_marks' => $obtained,
                                    'full_marks'     => $full,
                                    'pass_marks'     => (float) ($config['pass'] ?? 0),
                                ]
                            );
                            $saved++;
                        }
                    }
                } else {
                    foreach ($subject['exam_components'] as $componentName => $config) {
                        $obtained = $this->marks[$student['id']][$subject['id']][0][$componentName] ?? null;
                        if ($obtained === null || $obtained === '') continue;

                        $obtained = (float) $obtained;
                        $full     = (float) ($config['full'] ?? 0);

                        if ($obtained > $full) {
                            $this->errors_[] = "Student {$student['name']}: {$subject['name']} {$componentName} exceeds max ({$full}).";
                            continue;
                        }

                        Mark::updateOrCreate(
                            [
                                'student_id'     => $student['id'],
                                'subject_id'     => $subject['id'],
                                'sub_subject_id' => null,
                                'exam_id'        => $this->examId,
                                'component'      => $componentName,
                            ],
                            [
                                'obtained_marks' => $obtained,
                                'full_marks'     => $full,
                                'pass_marks'     => (float) ($config['pass'] ?? 0),
                            ]
                        );
                        $saved++;
                    }
                }
            }
        }

        $this->saving = false;

        if (!$silent) {
            if (empty($this->errors_)) {
                session()->flash('success', "{$saved} marks saved successfully.");
            } else {
                session()->flash('warning', count($this->errors_) . ' validation issues found. Valid marks were saved.');
            }
        }
    }

    public function saveMarksSilent(): void
    {
        if (!$this->autoSaveEnabled) return;
        $this->saveMarks(true);
    }

    public function saveAndCalculateMarks(ResultCalculationService $service): void
    {
        $this->saveMarks();

        if (!$this->loaded || $this->examId === null || empty($this->students)) {
            return;
        }

        $exam = Exam::find($this->examId);
        if (!$exam) return;

        $calculatedCount = 0;
        
        $preloadedMarks = Mark::where('exam_id', $exam->id)
            ->whereIn('student_id', array_column($this->students, 'id'))
            ->get()
            ->groupBy(function ($m) {
                return $m->subject_id . '_' . ($m->sub_subject_id ?? 0) . '_' . $m->component;
            });

        foreach ($this->students as $studentData) {
            $student = Student::find($studentData['id']);
            if ($student) {
                $studentMarks = $preloadedMarks->filter(function($marks, $key) use ($student) {
                     return $marks->first()->student_id == $student->id;
                });
                
                $service->calculateForStudent($student, $exam, $studentMarks);
                $calculatedCount++;
            }
        }
        
        $service->updateRanksForExam($exam);

        session()->flash('success', "Marks saved and results calculated for {$calculatedCount} students.");
    }

    private function recalculateAll(): void
    {
        $gradeMap = $this->getGradeMap();

        foreach ($this->students as $student) {
            $sid       = $student['id'];
            $total     = 0;
            $fullTotal = 0;
            $allPassed = true;

            foreach ($this->subjects as $subject) {
                if ($subject['has_sub_subjects']) {
                    $aggregatedComponents = [];

                    foreach ($subject['sub_subjects'] as $sub) {
                        foreach ($sub['exam_components'] as $componentName => $config) {
                            $obtained  = (float) ($this->marks[$sid][$subject['id']][$sub['id']][$componentName] ?? 0);
                            $pass      = (float) ($config['pass'] ?? 0);
                            $full      = (float) ($config['full'] ?? 0);

                            $total     += $obtained;
                            $fullTotal += $full;

                            if (!isset($aggregatedComponents[$componentName])) {
                                $aggregatedComponents[$componentName] = ['obtained' => 0, 'pass' => 0];
                            }
                            $aggregatedComponents[$componentName]['obtained'] += $obtained;
                            $aggregatedComponents[$componentName]['pass'] += $pass;
                        }
                    }

                    foreach ($aggregatedComponents as $compName => $data) {
                        if ($data['obtained'] < $data['pass']) {
                            $allPassed = false;
                        }
                    }
                } else {
                    foreach ($subject['exam_components'] as $componentName => $config) {
                        $obtained  = (float) ($this->marks[$sid][$subject['id']][0][$componentName] ?? 0);
                        $pass      = (float) ($config['pass'] ?? 0);
                        $full      = (float) ($config['full'] ?? 0);

                        $total     += $obtained;
                        $fullTotal += $full;

                        if ($obtained < $pass) {
                            $allPassed = false;
                        }
                    }
                }
            }

            $pct = $fullTotal > 0 ? round(($total / $fullTotal) * 100, 2) : 0;
            $isPassed = $allPassed && $pct >= 33;
            
            if (!$isPassed) {
                $grade = 'F';
                $gpa = 0.00;
            } else {
                [$grade, $gpa] = $this->resolveGrade($pct, $gradeMap);
            }

            $this->rowTotals[$sid]      = $total;
            $this->rowPercentages[$sid] = $pct;
            $this->rowGpas[$sid]        = $gpa;
            $this->rowGrades[$sid]      = $grade;
            $this->rowPassed[$sid]      = $isPassed;
        }
    }

    private function resolveGrade(float $pct, array $gradeMap): array
    {
        foreach ($gradeMap as $config) {
            if ($pct >= $config['min'] && $pct <= $config['max']) {
                return [$config['grade'], $config['gpa']];
            }
        }
        return ['F', 0.00];
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

        $availableSubjects = ($this->classId && $this->sectionId)
            ? Subject::where('class_id', $this->classId)
                     ->where('section_id', $this->sectionId)
                     ->orderBy('sort_order')
                     ->get()
            : collect();

        return view('livewire.marks-entry', compact('classes', 'sections', 'exams', 'availableSubjects'));
    }
}
