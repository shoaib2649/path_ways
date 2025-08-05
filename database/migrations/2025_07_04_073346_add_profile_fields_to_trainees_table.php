<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('training_and_hirings', function (Blueprint $table) {
            $table->string('specialization')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->integer('experience_years')->nullable();
            $table->text('education')->nullable();
            $table->text('certifications')->nullable();
            $table->string('clinic_name')->nullable();
            $table->text('clinic_address')->nullable();
            $table->string('available_days')->nullable();
            $table->string('available_time')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->text('doctor_notes')->nullable();
            $table->decimal('consultation_fee', 8, 2)->nullable();
            $table->string('profile_slug')->nullable();
            $table->string('colour')->nullable();
        });
    }

    public function down()
    {
        Schema::table('training_and_hirings', function (Blueprint $table) {
            $table->dropColumn([
                'specialization',
                'license_number',
                'license_expiry_date',
                'experience_years',
                'education',
                'certifications',
                'clinic_name',
                'clinic_address',
                'available_days',
                'available_time',
                'is_verified',
                'doctor_notes',
                'consultation_fee',
                'profile_slug',
                'colour',
            ]);
        });
    }
};
