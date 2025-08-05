<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('patients', function (Blueprint $table) {
            $table->string('partner_given_name')->nullable()->after('id');
            $table->string('partner_family_name')->nullable()->after('partner_given_name');
        });
    }
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['partner_given_name', 'partner_family_name']);
        });
    }
};
