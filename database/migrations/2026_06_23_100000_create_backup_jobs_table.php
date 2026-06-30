<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_system_id')
                ->constrained('backup_systems')
                ->restrictOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('backup_type', 30);
            $table->string('schedule_text')->nullable();
            $table->string('expected_frequency', 30);
            $table->time('expected_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['backup_system_id', 'is_active']);
            $table->index(['backup_type', 'expected_frequency']);
            $table->index('expected_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_jobs');
    }
};
