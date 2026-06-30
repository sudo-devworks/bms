<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_alerts', function (Blueprint $table) {
            $table->id();

            $table->string('type', 50);
            $table->string('severity', 50);
            $table->string('title');
            $table->text('message')->nullable();

            $table->foreignId('backup_log_id')
                ->nullable()
                ->constrained('backup_logs')
                ->nullOnDelete();

            $table->foreignId('backup_job_id')
                ->nullable()
                ->constrained('backup_jobs')
                ->nullOnDelete();

            $table->foreignId('backup_system_id')
                ->nullable()
                ->constrained('backup_systems')
                ->nullOnDelete();

            $table->foreignId('backup_storage_id')
                ->nullable()
                ->constrained('backup_storages')
                ->nullOnDelete();

            $table->string('status', 50)->default('new');

            $table->timestamp('triggered_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['severity', 'status']);
            $table->index('triggered_at');

            $table->unique(
                ['type', 'backup_log_id'],
                'backup_alerts_type_log_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_alerts');
    }
};