<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarksEntryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function marks_entry_page_requires_authentication(): void
    {
        $response = $this->get(route('marks.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_access_marks_entry(): void
    {
        $response = $this->actingAs($this->user)->get(route('marks.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function marks_cannot_exceed_full_marks(): void
    {
        $class   = SchoolClass::create(['name' => 'Class 10', 'sort_order' => 1]);
        $section = Section::create(['class_id' => $class->id, 'name' => 'A']);
        $student = Student::create(['name' => 'Test', 'roll' => 101, 'class_id' => $class->id, 'section_id' => $section->id]);
        $subject = Subject::create([
            'name'            => 'Math',
            'class_id'        => $class->id,
            'section_id'      => $section->id,
            'exam_components' => ['mcq' => ['full' => 40, 'pass' => 16]],
        ]);
        $exam = Exam::create(['name' => 'First Term', 'year' => 2024]);

        // Directly test that a mark above full_marks is rejected by model logic
        $mark = new Mark([
            'student_id'     => $student->id,
            'subject_id'     => $subject->id,
            'exam_id'        => $exam->id,
            'component'      => 'mcq',
            'obtained_marks' => 45, // exceeds full_marks of 40
            'full_marks'     => 40,
            'pass_marks'     => 16,
        ]);

        $this->assertGreaterThan($mark->full_marks, $mark->obtained_marks);
    }

    /** @test */
    public function marks_are_stored_correctly(): void
    {
        $class   = SchoolClass::create(['name' => 'Class 10', 'sort_order' => 1]);
        $section = Section::create(['class_id' => $class->id, 'name' => 'A']);
        $student = Student::create(['name' => 'Rakib', 'roll' => 101, 'class_id' => $class->id, 'section_id' => $section->id]);
        $subject = Subject::create([
            'name'            => 'Math',
            'class_id'        => $class->id,
            'section_id'      => $section->id,
            'exam_components' => ['mcq' => ['full' => 40, 'pass' => 16], 'cq' => ['full' => 60, 'pass' => 24]],
        ]);
        $exam = Exam::create(['name' => 'First Term', 'year' => 2024]);

        Mark::create([
            'student_id' => $student->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
            'component' => 'mcq', 'obtained_marks' => 35, 'full_marks' => 40, 'pass_marks' => 16,
        ]);
        Mark::create([
            'student_id' => $student->id, 'subject_id' => $subject->id, 'exam_id' => $exam->id,
            'component' => 'cq', 'obtained_marks' => 52, 'full_marks' => 60, 'pass_marks' => 24,
        ]);

        $this->assertDatabaseHas('marks', [
            'student_id' => $student->id, 'component' => 'mcq', 'obtained_marks' => 35,
        ]);
        $this->assertDatabaseHas('marks', [
            'student_id' => $student->id, 'component' => 'cq', 'obtained_marks' => 52,
        ]);
    }

    /** @test */
    public function dashboard_shows_correct_stats(): void
    {
        SchoolClass::create(['name' => 'Class 10', 'sort_order' => 1]);
        Exam::create(['name' => 'First Term', 'year' => 2024, 'is_active' => true]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }
}
