<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_code')->unique();
            $table->string('full_name');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('date_of_birth')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('blood_group')->nullable();
            $table->text('chief_complaint')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->string('doctor_name')->nullable();
            $table->enum('status', ['Active', 'Completed', 'Follow-up'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};