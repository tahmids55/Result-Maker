<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_configs', function (Blueprint $table) {
            $table->id();
            $table->string('grade');       // A+, A, A-, B+, etc.
            $table->decimal('gpa', 4, 2);  // 5.00, 4.00, 3.50, etc.
            $table->decimal('min_percentage', 5, 2);
            $table->decimal('max_percentage', 5, 2);
            $table->string('label')->nullable(); // Excellent, Very Good, etc.
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_configs');
    }
};
