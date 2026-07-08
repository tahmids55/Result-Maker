<?php

namespace Tests\Unit;

use App\Models\Exam;
use App\Models\GradeConfig;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Services\ResultCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultCalculationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ResultCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ResultCalculationService();
        $this->seedGrades();
    }

    private function seedGrades(): void
    {
        $grades = [
            ['grade' => 'A+', 'gpa' => 5.00, 'min_percentage' => 80,   'max_percentage' => 100,  'sort_order' => 1],
            ['grade' => 'A',  'gpa' => 4.00, 'min_percentage' => 70,   'max_percentage' => 79.99,'sort_order' => 2],
            ['grade' => 'F',  'gpa' => 0.00, 'min_percentage' => 0,    'max_percentage' => 32.99,'sort_order' => 7],
        ];
        foreach ($grades as $g) GradeConfig::create($g);
    }

    private function createBasicSetup(): array
    {
        $class   = SchoolClass::create(['name' => 'Class 10', 'sort_order' => 1]);
        $section = Section::create(['class_id' => $class->id, 'name' => 'A']);
        $student = Student::create([
            'name'       => 'Test Student',
            'roll'       => 101,
            'class_id'   => $class->id,
            'section_id' => $section->id,
        ]);
        $subject = Subject::create([
            'name'            => 'Mathematics',
            'class_id'        => $class->id,
            'section_id'      => $section->id,
            'exam_components' => ['mcq' => ['full' => 40, 'pass' => 16], 'cq' => ['full' => 60, 'pass' => 24]],
        ]);
        $exam = Exam::create(['name' => 'First Term', 'year' => 2024]);

        return compact('class', 'section', 'student', 'subject', 'exam');
    }

    /** @test */
    public function it_calculates_result_correctly_for_a_passing_student(): void
    {
        ['student' => $student, 'subject' => $subject, 'exam' => $exam] = $this->createBasicSetup();

        Mark::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
            'component' => 'mcq', 'obtained_marks' => 35, 'full_marks' => 40, 'pass_marks' => 16]);
        Mark::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
            'component' => 'cq', 'obtained_marks' => 52, 'full_marks' => 60, 'pass_marks' => 24]);

        $result = $this->service->calculateForStudent($student, $exam);

        $this->assertEquals(87, $result->total_marks);
        $this->assertEquals(100, $result->full_marks);
        $this->assertEquals(87.00, $result->percentage);
        $this->assertEquals('A+', $result->grade);
        $this->assertEquals(5.00, $result->gpa);
        $this->assertTrue($result->is_passed);
    }

    /** @test */
    public function it_marks_student_as_failed_when_below_pass_marks(): void
    {
        ['student' => $student, 'subject' => $subject, 'exam' => $exam] = $this->createBasicSetup();

        Mark::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
            'component' => 'mcq', 'obtained_marks' => 10, 'full_marks' => 40, 'pass_marks' => 16]);
        Mark::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
            'component' => 'cq', 'obtained_marks' => 20, 'full_marks' => 60, 'pass_marks' => 24]);

        $result = $this->service->calculateForStudent($student, $exam);

        $this->assertFalse($result->is_passed);
        $this->assertEquals('Fail', $result->division);
    }

    /** @test */
    public function it_calculates_percentage_correctly(): void
    {
        ['student' => $student, 'subject' => $subject, 'exam' => $exam] = $this->createBasicSetup();

        Mark::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
            'component' => 'mcq', 'obtained_marks' => 20, 'full_marks' => 40, 'pass_marks' => 16]);
        Mark::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
            'component' => 'cq', 'obtained_marks' => 30, 'full_marks' => 60, 'pass_marks' => 24]);

        $result = $this->service->calculateForStudent($student, $exam);

        $this->assertEquals(50, $result->total_marks);
        $this->assertEquals(50.00, $result->percentage);
    }

    /** @test */
    public function it_assigns_ranks_correctly(): void
    {
        ['class' => $class, 'section' => $section, 'subject' => $subject, 'exam' => $exam] = $this->createBasicSetup();

        $student2 = Student::create(['name' => 'Student 2', 'roll' => 102, 'class_id' => $class->id, 'section_id' => $section->id]);
        $student1 = Student::where('roll', 101)->first();

        // Student 1: 87 marks
        foreach (['mcq' => [35, 40], 'cq' => [52, 60]] as $comp => [$ob, $full]) {
            Mark::create(['student_id' => $student1->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
                'component' => $comp, 'obtained_marks' => $ob, 'full_marks' => $full, 'pass_marks' => $full * 0.4]);
        }
        // Student 2: 70 marks
        foreach (['mcq' => [28, 40], 'cq' => [42, 60]] as $comp => [$ob, $full]) {
            Mark::create(['student_id' => $student2->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
                'component' => $comp, 'obtained_marks' => $ob, 'full_marks' => $full, 'pass_marks' => $full * 0.4]);
        }

        $this->service->calculateForClass($class->id, $section->id, $exam);

        $result1 = $student1->fresh()->getResultForExam($exam->id);
        $result2 = $student2->fresh()->getResultForExam($exam->id);

        $this->assertEquals(1, $result1->rank);
        $this->assertEquals(2, $result2->rank);
    }
}
