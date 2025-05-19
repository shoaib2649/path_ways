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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');

            $table->unsignedBigInteger('provider_id');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('location')->nullable();
            $table->string('repeat_type')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('color_primary')->nullable();
            $table->string('color_secondary')->nullable();
            $table->text('actions')->nullable();
            $table->boolean('all_day')->default(false);
            $table->boolean('resizable_before_start')->default(false);
            $table->boolean('resizable_after_end')->default(false);
            $table->boolean('draggable')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
