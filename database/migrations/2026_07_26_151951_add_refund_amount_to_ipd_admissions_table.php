<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->decimal('refund_amount', 10, 2)->default(0)->after('advance_paid');
        });
    }

    public function down(): void
    {
        Schema::table('ipd_admissions', function (Blueprint $table) {
            $table->dropColumn('refund_amount');
        });
    }
};