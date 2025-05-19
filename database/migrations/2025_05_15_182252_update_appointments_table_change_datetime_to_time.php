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
        Schema::table('appointments', function (Blueprint $table) {
            //
            // Drop existing datetime columns
            $table->dropColumn(['start', 'end','date']);

            // Add new time-only columns
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Add new date column
            $table->date('appointment_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            //
            // Rollback: remove new columns
            $table->dropColumn(['start_time', 'end_time', 'appointment_date']);

            // Restore original datetime columns
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
        });
    }
};
