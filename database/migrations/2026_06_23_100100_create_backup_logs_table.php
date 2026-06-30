<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_job_id')
                ->constrained('backup_jobs')
                ->restrictOnDelete();
            $table->foreignId('backup_system_id')
                ->constrained('backup_systems')
                ->restrictOnDelete();
            $table->foreignId('backup_storage_id')
                ->constrained('backup_storages')
                ->restrictOnDelete();
            $table->string('status', 20);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->date('backup_date');
            $table->string('file_name')->nullable();
            $table->string('file_path', 1000)->nullable();
            $table->unsignedBigInteger('file_size_bytes')->nullable();
            $table->string('checksum')->nullable();
            $table->text('message')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['backup_date', 'status']);
            $table->index(['backup_job_id', 'backup_date']);
            $table->index(['backup_system_id', 'backup_date']);
            $table->index(['backup_storage_id', 'backup_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};
