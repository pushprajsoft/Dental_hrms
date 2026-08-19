<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_settings', function (Blueprint $table) {
            $table->boolean('auto_schedule_enabled')->default(false)->after('thank_you_template');
            $table->time('scheduled_time')->default('21:00:00')->after('auto_schedule_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_settings', function (Blueprint $table) {
            $table->dropColumn(['auto_schedule_enabled', 'scheduled_time']);
        });
    }
};