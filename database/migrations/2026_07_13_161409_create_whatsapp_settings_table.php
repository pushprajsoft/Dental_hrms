<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('country_code')->default('91');           // used if a patient's phone has no country code
            $table->string('support_number')->nullable();             // your clinic's own WhatsApp support line
            $table->text('thank_you_template')->default(
                'Hi {name}, thank you for visiting DentaCare Clinic today! We hope you had a great experience. Feel free to reach out if you have any questions. 🦷'
            );
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};