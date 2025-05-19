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
        Schema::table('provider_availability_slots', function (Blueprint $table) {
            //
             $table->date('date')->nullable()->after('day_of_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_availability_slots', function (Blueprint $table) {
            //
             $table->dropColumn('date');
        });
    }
};
