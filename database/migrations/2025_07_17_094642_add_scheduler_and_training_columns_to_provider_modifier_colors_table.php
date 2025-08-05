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
        Schema::table('provider_modifier_colors', function (Blueprint $table) {
            $table->unsignedBigInteger('training_and_hiring_id')->nullable()->after('provider_id');
            $table->unsignedBigInteger('scheduler_id')->nullable()->after('training_and_hiring_id');
        });
    }

    public function down(): void
    {
        Schema::table('provider_modifier_colors', function (Blueprint $table) {
            $table->dropColumn(['training_and_hiring_id', 'scheduler_id']);
        });
    }
};
