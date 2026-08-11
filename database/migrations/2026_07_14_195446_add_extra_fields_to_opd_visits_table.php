<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opd_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('opd_visits', 'token_number')) {
                $table->string('token_number')->nullable()->after('visit_code');
            }
            if (!Schema::hasColumn('opd_visits', 'mlc')) {
                $table->enum('mlc', ['Yes', 'No'])->default('No')->after('token_number');
            }
            if (!Schema::hasColumn('opd_visits', 'referred_by')) {
                $table->string('referred_by')->nullable()->after('mlc');
            }

            // Vitals
            if (!Schema::hasColumn('opd_visits', 'height_cm')) {
                $table->decimal('height_cm', 5, 1)->nullable();
            }
            if (!Schema::hasColumn('opd_visits', 'weight_kg')) {
                $table->decimal('weight_kg', 5, 1)->nullable();
            }
            if (!Schema::hasColumn('opd_visits', 'blood_pressure')) {
                $table->string('blood_pressure', 20)->nullable();
            }
            if (!Schema::hasColumn('opd_visits', 'pulse_rate')) {
                $table->integer('pulse_rate')->nullable();
            }
            if (!Schema::hasColumn('opd_visits', 'temperature')) {
                $table->decimal('temperature', 4, 1)->nullable();
            }
            if (!Schema::hasColumn('opd_visits', 'spo2')) {
                $table->integer('spo2')->nullable();
            }
            if (!Schema::hasColumn('opd_visits', 'symptoms')) {
                $table->text('symptoms')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('opd_visits', function (Blueprint $table) {
            $columns = [
                'token_number', 'mlc', 'referred_by',
                'height_cm', 'weight_kg', 'blood_pressure',
                'pulse_rate', 'temperature', 'spo2', 'symptoms',
            ];

            $existing = array_filter($columns, fn ($col) => Schema::hasColumn('opd_visits', $col));

            if (!empty($existing)) {
                $table->dropColumn($existing);
            }
        });
    }
};