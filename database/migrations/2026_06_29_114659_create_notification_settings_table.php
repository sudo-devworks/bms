<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();

            $table->string('channel', 50)->default('email');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email');
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['channel', 'is_active']);
            $table->unique(['channel', 'recipient_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};