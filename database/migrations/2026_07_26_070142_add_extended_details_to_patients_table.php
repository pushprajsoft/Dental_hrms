<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('age')->nullable()->after('date_of_birth');
            $table->string('aadhar')->nullable()->after('phone');
            $table->string('mlc')->default('No')->after('status');
            $table->string('fh_name')->nullable()->after('mlc');
            $table->string('mother_name')->nullable()->after('fh_name');
            $table->string('marital_status')->nullable()->after('mother_name');
            
            $table->string('rel_name')->nullable()->after('marital_status');
            $table->string('rel_relation')->nullable()->after('rel_name');
            $table->string('rel_contact')->nullable()->after('rel_relation');
            $table->text('rel_address')->nullable()->after('rel_contact');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['age', 'aadhar', 'mlc', 'fh_name', 'mother_name', 'marital_status', 'rel_name', 'rel_relation', 'rel_contact', 'rel_address']);
        });
    }
};