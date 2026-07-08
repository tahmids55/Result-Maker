<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add has_sub_subjects to subjects
        Schema::table('subjects', function (Blueprint $table) {
            $table->boolean('has_sub_subjects')->default(false)->after('exam_components');
        });

        // Create sub_subjects table
        Schema::create('sub_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('name');
            $table->json('exam_components');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add sub_subject_id to marks
        Schema::table('marks', function (Blueprint $table) {
            $table->foreignId('sub_subject_id')->nullable()->after('subject_id')->constrained('sub_subjects')->onDelete('cascade');
        });
        
        // We need to drop the old unique constraint on marks and recreate it with sub_subject_id
        Schema::table('marks', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'subject_id', 'exam_id', 'component']);
            $table->unique(['student_id', 'subject_id', 'sub_subject_id', 'exam_id', 'component'], 'marks_unique_component');
        });
    }

    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            $table->dropUnique('marks_unique_component');
            $table->unique(['student_id', 'subject_id', 'exam_id', 'component']);
            $table->dropForeign(['sub_subject_id']);
            $table->dropColumn('sub_subject_id');
        });

        Schema::dropIfExists('sub_subjects');

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('has_sub_subjects');
        });
    }
};
