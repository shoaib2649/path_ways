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
        Schema::create('insurance_patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insurance_provider_id');
            $table->unsignedBigInteger('patient_id');
            $table->foreign('insurance_provider_id')->references('id')->on('insurance_providers')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_patients');
    }
};
