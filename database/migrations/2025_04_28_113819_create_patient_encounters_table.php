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
        Schema::create('patient_encounters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->unsignedBigInteger('facility_id')->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities');
            $table->unsignedBigInteger('speciality_id')->nullable();
            $table->foreign('speciality_id')->references('id')->on('list_options');
            $table->unsignedBigInteger('encounter_type_id')->nullable();
            $table->foreign('encounter_type_id')->references('id')->on('list_options');
            $table->date('encounter_date')->nullable();
            $table->string('visit_reason')->nullable();
            $table->string('provider')->nullable();
            $table->string('speciality')->nullable();
            $table->string('encounter_type')->nullable();
            $table->enum('encounter_status', ['active', 'inactive', 'draft', 'signed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_encounters');
    }
};
