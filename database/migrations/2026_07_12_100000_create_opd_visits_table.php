<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opd_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_code')->unique();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->date('visit_date');
            $table->enum('visit_type', ['New', 'Follow-up', 'Revisit'])->default('New');
            $table->text('chief_complaint')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('payment_method', ['Cash', 'UPI', 'Pending'])->default('Pending');
            $table->date('payment_date')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->enum('status', ['Paid', 'Partial', 'Pending', 'Refunded'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opd_visits');
    }
};