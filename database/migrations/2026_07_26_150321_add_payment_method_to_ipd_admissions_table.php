<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'UPI', 'Card', 'Pending'])->default('Cash')->after('advance_paid');
        });
    }

    public function down(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};