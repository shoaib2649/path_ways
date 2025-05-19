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
        Schema::create('list_options', function (Blueprint $table) {
            $table->id();
            $table->string('list_type')->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->string('sequence')->nullable();
            $table->string('is_default')->nullable();
            $table->string('option_value')->nullable();
            $table->string('mapping')->nullable();
            $table->string('notes')->nullable();
            $table->string('codes')->nullable();
            $table->string('toggle_setting_1')->nullable();
            $table->string('toggle_setting_2')->nullable();
            $table->string('activity')->nullable();
            $table->string('subtype')->nullable();
            $table->string('edit_options')->nullable();
            $table->unique(['slug', 'list_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_options');
    }
};
