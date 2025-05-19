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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('mrn', 20)->unique()->nullable();
            $table->string('mr')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('suffix', 10)->nullable();
            $table->string('social_security_number')->nullable();
            $table->string('type')->nullable();
            $table->string('individual_appointments')->nullable();
            $table->string('group_appointments')->nullable();
            $table->string('blood_score')->default(0)->nullable();
            $table->string('lifestyle_score')->default(0)->nullable();
            $table->string('supplement_medication_score')->default(0)->nullable();
            $table->string('physical_vital_sign_score')->default(0)->nullable();
            $table->text('image')->nullable();
            $table->string('module_level')->default(0)->nullable();;
            $table->string('referred_by')->nullable();
            $table->string('wait_list')->default('y');

            $table->text('qualification')->nullable();
            $table->string('provider_name')->nullable();
            $table->string('status')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
