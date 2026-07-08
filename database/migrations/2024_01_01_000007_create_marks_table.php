<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->string('component'); // mcq, cq, practical, viva, etc.
            $table->decimal('obtained_marks', 8, 2)->default(0);
            $table->decimal('full_marks', 8, 2);
            $table->decimal('pass_marks', 8, 2);
            $table->boolean('is_absent')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'exam_id', 'component']);
            $table->index(['student_id', 'exam_id']);
            $table->index(['subject_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
