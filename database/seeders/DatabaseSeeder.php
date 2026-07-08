<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\GradeConfig;
use App\Models\Mark;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ──────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@markscraft.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        // ── School ──────────────────────────────────────────────────
        School::updateOrCreate(
            ['id' => 1],
            [
                'name'        => 'Greenfield High School',
                'address'     => '123 School Road, Dhaka, Bangladesh',
                'phone'       => '01700-000000',
                'email'       => 'info@greenfieldhigh.edu.bd',
                'footer_text' => 'This is a computer generated marksheet. No signature required.',
                'date_format' => 'd/m/Y',
                'gpa_scale'   => '5.0',
            ]
        );

        // ── Grading System (Bangladesh SSC/HSC Scale) ───────────────
        GradeConfig::truncate();
        $grades = [
            ['grade' => 'A+', 'gpa' => 5.00, 'min_percentage' => 80,    'max_percentage' => 100,  'label' => 'Excellent',   'sort_order' => 1],
            ['grade' => 'A',  'gpa' => 4.00, 'min_percentage' => 70,    'max_percentage' => 79.99,'label' => 'Very Good',   'sort_order' => 2],
            ['grade' => 'A-', 'gpa' => 3.50, 'min_percentage' => 60,    'max_percentage' => 69.99,'label' => 'Good',        'sort_order' => 3],
            ['grade' => 'B',  'gpa' => 3.00, 'min_percentage' => 50,    'max_percentage' => 59.99,'label' => 'Satisfactory','sort_order' => 4],
            ['grade' => 'C',  'gpa' => 2.00, 'min_percentage' => 40,    'max_percentage' => 49.99,'label' => 'Average',     'sort_order' => 5],
            ['grade' => 'D',  'gpa' => 1.00, 'min_percentage' => 33,    'max_percentage' => 39.99,'label' => 'Poor',        'sort_order' => 6],
            ['grade' => 'F',  'gpa' => 0.00, 'min_percentage' => 0,     'max_percentage' => 32.99,'label' => 'Fail',        'sort_order' => 7],
        ];
        foreach ($grades as $g) GradeConfig::create([
            "user_id" => 1,$g);

        // ── Classes ─────────────────────────────────────────────────
        $classNames = ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5',
                       'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10'];
        $classModels = [];
        foreach ($classNames as $i => $name) {
            $classModels[$name] = SchoolClass::updateOrCreate(
                ['name' => $name],
                ['sort_order' => $i + 1]
            );
        }

        // ── Sections for Class 10 ────────────────────────────────────
        $class10   = $classModels['Class 10'];
        $sectionA  = Section::updateOrCreate(['class_id' => $class10->id, 'name' => 'A']);
        $sectionB  = Section::updateOrCreate(['class_id' => $class10->id, 'name' => 'B']);

        // ── Subjects for Class 10 Section A ─────────────────────────
        $subjectDefs = [
            ['name' => 'Bangla',      'code' => 'BAN', 'components' => ['mcq' => [40, 16], 'cq' => [60, 24]]],
            ['name' => 'English',     'code' => 'ENG', 'components' => ['mcq' => [40, 16], 'cq' => [60, 24]]],
            ['name' => 'Mathematics', 'code' => 'MAT', 'components' => ['mcq' => [40, 16], 'cq' => [60, 24]]],
            ['name' => 'Physics',     'code' => 'PHY', 'components' => ['mcq' => [25, 10], 'cq' => [50, 20], 'practical' => [25, 13]]],
            ['name' => 'Chemistry',   'code' => 'CHM', 'components' => ['mcq' => [25, 10], 'cq' => [50, 20], 'practical' => [25, 13]]],
            ['name' => 'Biology',     'code' => 'BIO', 'components' => ['mcq' => [25, 10], 'cq' => [50, 20], 'practical' => [25, 13]]],
            ['name' => 'ICT',         'code' => 'ICT', 'components' => ['mcq' => [30, 12], 'practical' => [70, 28]]],
        ];

        $subjects = [];
        foreach ($subjectDefs as $i => $def) {
            $components = [];
            foreach ($def['components'] as $compName => [$full, $pass]) {
                $components[$compName] = ['full' => $full, 'pass' => $pass];
            }
            $subjects[] = Subject::updateOrCreate(
                ['name' => $def['name'], 'class_id' => $class10->id, 'section_id' => $sectionA->id],
                ['code' => $def['code'], 'exam_components' => $components, 'sort_order' => $i + 1]
            );
        }

        // ── Students for Class 10A ───────────────────────────────────
        $studentDefs = [
            ['name' => 'Rakib Hasan',    'roll' => 101, 'father' => 'Abdul Hasan',    'mother' => 'Fatema Begum'],
            ['name' => 'Priya Sharma',   'roll' => 102, 'father' => 'Rajesh Sharma',  'mother' => 'Sunita Sharma'],
            ['name' => 'Tanvir Ahmed',   'roll' => 103, 'father' => 'Karim Ahmed',    'mother' => 'Rahela Begum'],
            ['name' => 'Sadia Islam',    'roll' => 104, 'father' => 'Nazrul Islam',   'mother' => 'Roksana Begum'],
            ['name' => 'Mehedi Hasan',   'roll' => 105, 'father' => 'Nurul Hasan',    'mother' => 'Hamida Begum'],
            ['name' => 'Fatima Akter',   'roll' => 106, 'father' => 'Rafiqul Islam',  'mother' => 'Nasrin Akter'],
            ['name' => 'Arif Hossain',   'roll' => 107, 'father' => 'Jamal Hossain',  'mother' => 'Lipi Begum'],
            ['name' => 'Nadia Rahman',   'roll' => 108, 'father' => 'Habibur Rahman', 'mother' => 'Morjina Begum'],
            ['name' => 'Sabbir Khan',    'roll' => 109, 'father' => 'Monir Khan',     'mother' => 'Sumaiya Begum'],
            ['name' => 'Mitu Khatun',    'roll' => 110, 'father' => 'Azizul Haque',   'mother' => 'Taslima Begum'],
        ];

        $studentModels = [];
        foreach ($studentDefs as $def) {
            $studentModels[] = Student::updateOrCreate(
                ['roll' => $def['roll'], 'class_id' => $class10->id, 'section_id' => $sectionA->id],
                [
                    'name'        => $def['name'],
                    'father_name' => $def['father'],
                    'mother_name' => $def['mother'],
                    'session'     => '2024-2025',
                ]
            );
        }

        // ── Exam ─────────────────────────────────────────────────────
        $exam = Exam::updateOrCreate(
            ['name' => 'First Term', 'year' => 2024],
            ['start_date' => '2024-03-01', 'end_date' => '2024-03-15', 'is_active' => true]
        );

        // ── Sample Marks ─────────────────────────────────────────────
        $sampleMarks = [
            101 => ['Bangla' => ['mcq' => 34, 'cq' => 50], 'English' => ['mcq' => 35, 'cq' => 52], 'Mathematics' => ['mcq' => 38, 'cq' => 57]],
            102 => ['Bangla' => ['mcq' => 30, 'cq' => 45], 'English' => ['mcq' => 28, 'cq' => 40], 'Mathematics' => ['mcq' => 25, 'cq' => 38]],
            103 => ['Bangla' => ['mcq' => 36, 'cq' => 54], 'English' => ['mcq' => 33, 'cq' => 48], 'Mathematics' => ['mcq' => 40, 'cq' => 59]],
        ];

        foreach ($studentModels as $student) {
            $rollMarks = $sampleMarks[$student->roll] ?? null;
            if (!$rollMarks) continue;

            foreach ($subjects as $subject) {
                $compData = $rollMarks[$subject->name] ?? null;
                if (!$compData) continue;

                foreach ($subject->exam_components as $compName => $config) {
                    $obtained = $compData[$compName] ?? rand((int)($config['pass']), (int)($config['full']));
                    Mark::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'exam_id'    => $exam->id,
                            'component'  => $compName,
                        ],
                        [
                            'obtained_marks' => $obtained,
                            'full_marks'     => $config['full'],
                            'pass_marks'     => $config['pass'],
                        ]
                    );
                }
            }
        }

        $this->command->info('✔ MarksCraft seeded: admin@markscraft.com / password');
        $this->command->info('✔ Class 10A with 10 students, 7 subjects, 1 exam, sample marks');
    }
}
