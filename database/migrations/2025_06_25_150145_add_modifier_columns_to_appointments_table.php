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
            $table->string('modifier_1')->nullable();
            $table->string('modifier_2')->nullable();
            $table->string('modifier_3')->nullable();
            $table->string('modifier_4')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['modifier_1', 'modifier_2', 'modifier_3', 'modifier_4']);
        });
    }
};
