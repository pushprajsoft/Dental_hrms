<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_name')->default('DentaCare Clinic');
            $table->text('hospital_address')->nullable();
            $table->string('hospital_phone')->nullable();
            $table->string('hospital_email')->nullable();
            $table->string('gst_number')->nullable();
            $table->text('header_html')->nullable(); // For the Word-like editor
            $table->text('footer_html')->nullable(); // For Terms & Conditions
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_settings');
    }
};