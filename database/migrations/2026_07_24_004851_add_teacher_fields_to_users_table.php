<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('role')->default('admin')->after('email'); // 'admin' or 'teacher'
            $table->foreignId('admin_id')->nullable()->after('role')
                  ->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['username', 'role', 'admin_id']);
        });
    }
};
