<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_systems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_storage_id')
                ->constrained('backup_storages')
                ->restrictOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('category');
            $table->string('source_server')->nullable();
            $table->string('source_path', 500)->nullable();
            $table->string('backup_schedule')->nullable();
            $table->string('expected_frequency');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['backup_storage_id', 'is_active']);
            $table->index(['category', 'expected_frequency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_systems');
    }
};