<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_admissions', function (Blueprint $table) {
            $table->id();
            $table->string('ipd_code')->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('admission_date');
            $table->date('discharge_date')->nullable();
            $table->string('room_number')->nullable();
            $table->string('bed_number')->nullable();
            $table->decimal('advance_paid', 10, 2)->default(0);
            $table->enum('status', ['Admitted', 'Discharged'])->default('Admitted');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_admissions');
    }
};