<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ipd_admissions');
        Schema::create('ipd_admissions', function (Blueprint $table) {
            $table->id();
            $table->string('ipd_code')->unique();
            
            // Admission Details
            $table->dateTime('admission_date');
            $table->string('registered_type')->default('New');
            $table->foreignId('patient_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('scheme_type')->nullable();
            $table->string('scheme_name')->nullable();
            $table->string('case_type')->nullable();
            $table->string('bill_category')->nullable();
            $table->boolean('corporate')->default(false);
            $table->string('esic_no')->nullable();
            $table->string('urn_no')->nullable();
            $table->text('admission_note')->nullable();
            $table->string('referral_doctor')->nullable();
            $table->text('remark')->nullable();
            
            // Patient Personal Details (Captured at admission)
            $table->string('p_name')->nullable();
            $table->string('p_gender')->nullable();
            $table->date('p_dob')->nullable();
            $table->string('p_age')->nullable();
            $table->string('p_mobile')->nullable();
            $table->string('p_aadhar')->nullable();
            $table->text('p_address')->nullable();
            $string = $table->string('p_mlc')->default('No');
            $table->string('p_fh_name')->nullable();
            $table->string('p_mother_name')->nullable();
            $table->string('p_marital_status')->nullable();
            
            // Relative Details
            $table->string('rel_name')->nullable();
            $table->string('rel_relation')->nullable();
            $table->string('rel_contact')->nullable();
            $table->text('rel_address')->nullable();
            
            // Consultant Details
            $table->foreignId('doctor_id')->nullable()->constrained()->cascadeOnDelete(); // Incharge Consultant
            $table->foreignId('attending_doctor_id')->nullable()->constrained('doctors')->cascadeOnDelete();
            
            // Financial & Status
            $table->decimal('advance_paid', 10, 2)->default(0);
            $table->enum('status', ['Admitted', 'Discharged'])->default('Admitted');
            $table->date('discharge_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_admissions');
    }
};