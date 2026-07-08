<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'schools',
        'classes',
        'sections',
        'students',
        'subjects',
        'exams',
        'marks',
        'results',
        'marksheet_templates',
        'generated_marksheets',
        'ocr_imports',
        'sms_logs',
        'grade_configs'
    ];

    public function up(): void
    {
        // First get the admin user id
        $adminId = \Illuminate\Support\Facades\DB::table('users')->where('email', 'admin@markscraft.com')->value('id') ?? 1;

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'user_id')) {
                        $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    }
                });
                
                // Set existing records to belong to admin
                \Illuminate\Support\Facades\DB::table($tableName)->update(['user_id' => $adminId]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'user_id')) {
                        $table->dropColumn('user_id');
                    }
                });
            }
        }
    }
};
