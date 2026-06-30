<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('backup_jobs', 'expected_run_time')) {
                $table->time('expected_run_time')->nullable()->after('is_active');
            }

            if (! Schema::hasColumn('backup_jobs', 'alert_after_minutes')) {
                $table->unsignedInteger('alert_after_minutes')->default(60)->after('expected_run_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('backup_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('backup_jobs', 'alert_after_minutes')) {
                $table->dropColumn('alert_after_minutes');
            }

            if (Schema::hasColumn('backup_jobs', 'expected_run_time')) {
                $table->dropColumn('expected_run_time');
            }
        });
    }
};