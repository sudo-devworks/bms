<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_storages', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('os_type', 20);
            $table->string('access_type', 20);

            $table->string('host')->nullable();
            $table->string('base_path');

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('os_type');
            $table->index('access_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_storages');
    }
};