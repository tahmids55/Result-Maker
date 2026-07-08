<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->decimal('total_marks', 10, 2)->default(0);
            $table->decimal('full_marks', 10, 2)->default(0);
            $table->decimal('percentage', 8, 2)->default(0);
            $table->decimal('gpa', 4, 2)->default(0);
            $table->string('grade')->nullable();
            $table->string('division')->nullable();
            $table->boolean('is_passed')->default(false);
            $table->integer('rank')->nullable();
            // JSON of per-subject breakdown
            $table->json('subject_details')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'exam_id']);
            $table->index(['exam_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
