<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->string('bed_number')->unique();
            $table->string('room_number')->nullable();
            $table->enum('bed_type', ['General', 'Private', 'ICU', 'Ward'])->default('General');
            $table->decimal('charge_per_day', 10, 2)->default(0);
            $table->enum('status', ['Available', 'Occupied', 'Maintenance'])->default('Available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};