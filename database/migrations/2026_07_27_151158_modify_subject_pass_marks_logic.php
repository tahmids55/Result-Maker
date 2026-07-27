<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->decimal('full_marks', 8, 2)->default(100)->after('is_optional');
            $table->decimal('pass_marks', 8, 2)->default(33)->after('full_marks');
            $table->boolean('is_individual_pass')->default(false)->after('pass_marks');
            
            $table->dropColumn('accumulated_pass_marks');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->boolean('accumulated_pass_marks')->default(false);
            
            $table->dropColumn('full_marks');
            $table->dropColumn('pass_marks');
            $table->dropColumn('is_individual_pass');
        });
    }
};
