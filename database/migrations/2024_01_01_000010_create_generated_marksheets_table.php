<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_marksheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('marksheet_templates')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type')->default('docx'); // docx or pdf
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_marksheets');
    }
};
