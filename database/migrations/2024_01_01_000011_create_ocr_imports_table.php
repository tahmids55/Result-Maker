<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('image_path');
            $table->json('extracted_data')->nullable();
            $table->enum('status', ['pending', 'processing', 'processed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->string('language')->default('eng');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_imports');
    }
};
