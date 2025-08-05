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
        Schema::create('daily_availability_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('trainee_id')->nullable();
            $table->boolean('therapy')->default(false);
            $table->boolean('assessment')->default(false);
            $table->integer('therapy_patients')->default(0);
            $table->integer('assessment_patients')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_availability_summaries');
    }
};
