<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opd_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_visit_id')->constrained('opd_visits')->onDelete('cascade');
            $table->enum('method', ['Cash', 'UPI', 'Cheque', 'Card', 'Other']);
            $table->decimal('amount', 10, 2);
            $table->string('reference_no')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opd_payments');
    }
};