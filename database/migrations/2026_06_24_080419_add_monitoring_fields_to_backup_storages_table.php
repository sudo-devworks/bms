<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_storages', function (Blueprint $table) {
            $table->unsignedBigInteger('total_space_bytes')->nullable()->after('is_active');
            $table->unsignedBigInteger('used_space_bytes')->nullable()->after('total_space_bytes');
            $table->unsignedBigInteger('free_space_bytes')->nullable()->after('used_space_bytes');
            $table->decimal('usage_percent', 5, 2)->nullable()->after('free_space_bytes');

            $table->string('last_check_status', 20)->default('unknown')->after('usage_percent');
            $table->text('last_check_message')->nullable()->after('last_check_status');
            $table->timestamp('last_checked_at')->nullable()->after('last_check_message');
        });
    }

    public function down(): void
    {
        Schema::table('backup_storages', function (Blueprint $table) {
            $table->dropColumn([
                'total_space_bytes',
                'used_space_bytes',
                'free_space_bytes',
                'usage_percent',
                'last_check_status',
                'last_check_message',
                'last_checked_at',
            ]);
        });
    }
};