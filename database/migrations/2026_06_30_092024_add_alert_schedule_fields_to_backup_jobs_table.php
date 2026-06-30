<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_jobs', function (Blueprint $table) {
            $table->time('expected_run_time')->nullable()->after('is_active');
            $table->unsignedInteger('alert_after_minutes')->default(60)->after('expected_run_time');
        });
    }

    public function down(): void
    {
        Schema::table('backup_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'expected_run_time',
                'alert_after_minutes',
            ]);
        });
    }
};