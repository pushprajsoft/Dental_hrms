<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete()->after('attending_doctor_id');
            $table->date('allotment_date')->nullable()->after('bed_id');
        });
    }

    public function down(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->dropForeign(['bed_id']);
            $table->dropColumn(['bed_id', 'allotment_date']);
        });
    }
};