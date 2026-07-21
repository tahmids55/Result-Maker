<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('generated_marksheets', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->change();
            $table->string('batch_name')->nullable()->after('student_id');
        });
    }

    public function down(): void
    {
        Schema::table('generated_marksheets', function (Blueprint $table) {
            $table->dropColumn('batch_name');
            // Reverting to non-nullable might fail if there are batch records, so we shouldn't strictly enforce it in down,
            // but for simplicity we will just leave student_id nullable or try to revert.
            // $table->foreignId('student_id')->nullable(false)->change();
        });
    }
};
