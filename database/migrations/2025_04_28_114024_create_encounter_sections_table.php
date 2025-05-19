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
        Schema::create('encounter_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedBigInteger('facility_id')->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities');
            $table->unsignedBigInteger('encounter_id')->nullable();
            $table->foreign('encounter_id')->references('id')->on('patient_encounters');
            $table->text('chief_complaint')->nullable();
            $table->text('history')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('surgical_history')->nullable();
            $table->text('family_history')->nullable();
            $table->text('social_history')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('review_of_systems')->nullable();
            $table->text('physical_exam')->nullable();
            $table->text('vital_sign')->nullable();
            $table->text('assessments')->nullable();
            $table->text('procedure')->nullable();
            $table->text('follow_up')->nullable();
            $table->text('json_dump')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounter_sections');
    }
};
