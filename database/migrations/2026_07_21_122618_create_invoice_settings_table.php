<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name')->default('DentaCare Clinic');
            $table->text('clinic_address')->nullable();
            $table->string('clinic_phone')->nullable();
            $table->string('clinic_email')->nullable();
            $table->string('gst_number')->nullable();
            $table->boolean('gst_enabled')->default(false);
            $table->decimal('gst_percentage', 5, 2)->default(18.00);
            $table->text('footer_notes')->nullable()->default('Thank you for choosing DentaCare!');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};