<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_monitors', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('status')->default('unknown');
            $table->text('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->index(['key', 'status']);
            $table->index('last_run_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_monitors');
    }
};