<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\GradeConfig;
use Illuminate\Support\Collection;

class ResultCalculationService
{
    /**
     * Calculate and persist the result for one student in one exam.
     */
    public function calculateForStudent(Student $student, Exam $exam, ?Collection $preloadedMarks = null): Result
    {
        $subjects = Subject::with('subSubjects')->where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->orderBy('sort_order')
            ->get();

        // Pre-load all marks for this student+exam in a single query (unless already provided)
        if ($preloadedMarks === null) {
            $preloadedMarks = Mark::where('student_id', $student->id)
                ->where('exam_id', $exam->id)
                ->get()
                ->groupBy(function ($m) {
                    return $m->subject_id . '_' . ($m->sub_subject_id ?? 0) . '_' . $m->component;
                });
        }

        $totalObtained = 0;
        $totalFull     = 0;
        $isPassed      = true;
        $subjectDetails = [];
        $failedSubjects = 0;
        
        $totalGpa = 0.0;
        $normalSubjectCount = 0;

        foreach ($subjects as $subject) {
            $subjectObtained = 0;
            $subjectFull     = 0;
            $subjectPassed   = true;
            $componentDetails = [];
            $subSubjectDetails = [];

            if ($subject->has_sub_subjects) {
                $aggregatedComponents = [];

                foreach ($subject->subSubjects as $sub) {
                    $subObtained = 0;
                    $subFull = 0;
                    $subComponents = [];

                    $components = $sub->exam_components ?? [];
                    foreach ($components as $componentName => $componentConfig) {
                        $fullMarks = $componentConfig['full'] ?? 0;
                        $passMarks = $componentConfig['pass'] ?? 0;

                        $key = $subject->id . '_' . $sub->id . '_' . $componentName;
                        $mark = $preloadedMarks->get($key)?->first();

                        $obtained  = $mark?->obtained_marks ?? 0;
                        $isAbsent  = $mark?->is_absent ?? false;

                        $subObtained += $obtained;
                        $subFull     += $fullMarks;

                        $subComponents[$componentName] = [
                            'obtained'   => $obtained,
                            'full'       => $fullMarks,
                            'pass'       => $passMarks,
                            'is_absent'  => $isAbsent,
                        ];

                        if (!isset($aggregatedComponents[$componentName])) {
                            $aggregatedComponents[$componentName] = [
                                'obtained' => 0, 
                                'pass' => 0, 
                                'full' => 0, 
                                'is_absent' => false
                            ];
                        }
                        $aggregatedComponents[$componentName]['obtained'] += $obtained;
                        $aggregatedComponents[$componentName]['pass'] += $passMarks;
                        $aggregatedComponents[$componentName]['full'] += $fullMarks;
                        if ($isAbsent) {
                            $aggregatedComponents[$componentName]['is_absent'] = true;
                        }
                    }

                    $subPercentage = $subFull > 0 ? round(($subObtained / $subFull) * 100, 2) : 0;
                    $subGradeInfo = GradeConfig::resolve($subPercentage);

                    $subSubjectDetails[] = [
                        'sub_subject_id' => $sub->id,
                        'name'           => $sub->name,
                        'obtained'       => $subObtained,
                        'full'           => $subFull,
                        'percentage'     => $subPercentage,
                        'grade'          => $subGradeInfo['grade'],
                        'gpa'            => $subGradeInfo['gpa'],
                        'components'     => $subComponents,
                    ];

                    $subjectObtained += $subObtained;
                    $subjectFull     += $subFull;
                }

                foreach ($aggregatedComponents as $compName => $data) {
                    $compPassed = !$data['is_absent'] && ($data['obtained'] >= $data['pass']);
                    
                    $componentDetails[$compName] = [
                        'obtained'  => $data['obtained'],
                        'full'      => $data['full'],
                        'pass'      => $data['pass'],
                        'is_passed' => $compPassed,
                        'is_absent' => $data['is_absent'],
                    ];

                    if (!$compPassed) {
                        $subjectPassed = false;
                    }
                }
            } else {
                $components = $subject->exam_components ?? [];
                foreach ($components as $componentName => $componentConfig) {
                    $fullMarks = $componentConfig['full'] ?? 0;
                    $passMarks = $componentConfig['pass'] ?? 0;

                    $key = $subject->id . '_0_' . $componentName;
                    $mark = $preloadedMarks->get($key)?->first();

                    $obtained  = $mark?->obtained_marks ?? 0;
                    $isAbsent  = $mark?->is_absent ?? false;
                    $compPassed = !$isAbsent && ($obtained >= $passMarks);

                    if (!$compPassed) {
                        $subjectPassed = false;
                    }

                    $subjectObtained += $obtained;
                    $subjectFull     += $fullMarks;

                    $componentDetails[$componentName] = [
                        'obtained'   => $obtained,
                        'full'       => $fullMarks,
                        'pass'       => $passMarks,
                        'is_passed'  => $compPassed,
                        'is_absent'  => $isAbsent,
                    ];
                }
            }

            $subjectPercentage = $subjectFull > 0
                ? round(($subjectObtained / $subjectFull) * 100, 2)
                : 0;

            $gradeInfo = GradeConfig::resolve($subjectPercentage);

            if (!$subjectPassed) {
                // If a student fails any component (e.g. MCQ), the whole subject is failed, regardless of total percentage.
                $gradeInfo = ['grade' => 'F', 'gpa' => 0.00];
            }

            if ($subject->is_optional) {
                // For optional subject, failing it does not cause the student to fail the exam.
                // (Notice we DO NOT increment $failedSubjects here)
                
                // GPA bonus is (Optional GPA - 2), min 0. (max is used programmatically to bound above 0)
                $bonusGpa = max(0, $gradeInfo['gpa'] - 2.0);
                $totalGpa += $bonusGpa;
                
                // Optional marks bonus: exclude 40, add the rest.
                $bonusMarks = max(0, $subjectObtained - 40);
                $totalObtained += $bonusMarks;
                // Optional full marks are usually excluded from total full marks.
            } else {
                if (!$subjectPassed) {
                    $failedSubjects++;
                }
                
                $totalGpa += $gradeInfo['gpa'];
                $normalSubjectCount++;
                
                $totalObtained += $subjectObtained;
                $totalFull     += $subjectFull;
            }

            $subjectDetails[] = [
                'subject_id'       => $subject->id,
                'subject_name'     => $subject->name,
                'subject_code'     => $subject->code,
                'has_sub_subjects' => $subject->has_sub_subjects,
                'is_optional'      => $subject->is_optional,
                'obtained'         => $subjectObtained,
                'full'             => $subjectFull,
                'percentage'       => $subjectPercentage,
                'grade'            => $gradeInfo['grade'],
                'gpa'              => $gradeInfo['gpa'],
                'is_passed'        => $subjectPassed,
                'components'       => $componentDetails,
                'sub_subjects'     => $subSubjectDetails,
            ];
        }

        // Overall result
        $percentage  = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : 0;
        
        $averageGpa = $normalSubjectCount > 0 ? ($totalGpa / $normalSubjectCount) : 0;
        $finalGpa = min(5.00, round($averageGpa, 2));
        
        $isPassed = $failedSubjects === 0;
        
        if ($isPassed) {
            $finalGrade = GradeConfig::resolveFromGpa($finalGpa);
            $gradeInfo = ['grade' => $finalGrade, 'gpa' => $finalGpa];
        } else {
            $gradeInfo = ['grade' => 'F', 'gpa' => 0.00];
            $isPassed = false;
        }
        
        $division    = $this->resolveDivision($percentage, $isPassed);

        return Result::updateOrCreate(
            ['student_id' => $student->id, 'exam_id' => $exam->id],
            [
                'total_marks'    => $totalObtained,
                'full_marks'     => $totalFull,
                'percentage'     => $percentage,
                'gpa'            => $gradeInfo['gpa'],
                'grade'          => $gradeInfo['grade'],
                'division'       => $division,
                'is_passed'      => $isPassed,
                'subject_details'=> $subjectDetails,
            ]
        );
    }

    /**
     * Calculate results for all students in a class-section for an exam, then rank them.
     */
    public function calculateForClass(int $classId, int $sectionId, Exam $exam): Collection
    {
        $students = Student::where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->orderBy('roll')
            ->get();

        $results = $students->map(fn($s) => $this->calculateForStudent($s, $exam));

        // Assign ranks
        $this->assignRanks($exam->id, $classId, $sectionId);

        return $results;
    }

    /**
     * Assign merit ranks within a class-section for an exam.
     */
    public function assignRanks(int $examId, int $classId, int $sectionId): void
    {
        $studentIds = Student::where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->pluck('id');

        $results = Result::whereIn('student_id', $studentIds)
            ->where('exam_id', $examId)
            ->orderByDesc('total_marks')
            ->orderByDesc('percentage')
            ->get();

        $rank = 1;
        foreach ($results as $result) {
            $result->update(['rank' => $rank++]);
        }
    }

    /**
     * Specifically update ranks for all results of a given exam
     */
    public function updateRanksForExam(Exam $exam): void
    {
        // Get all unique class/section combinations for this exam's results
        $combinations = Result::where('exam_id', $exam->id)
            ->join('students', 'results.student_id', '=', 'students.id')
            ->select('students.class_id', 'students.section_id')
            ->distinct()
            ->get();
            
        foreach ($combinations as $combo) {
            $this->assignRanks($exam->id, $combo->class_id, $combo->section_id);
        }
    }

    private function resolveDivision(float $percentage, bool $passed): string
    {
        if (!$passed) return 'Fail';
        return match (true) {
            $percentage >= 80 => 'First Division',
            $percentage >= 60 => 'Second Division',
            $percentage >= 33 => 'Third Division',
            default           => 'Fail',
        };
    }
}
