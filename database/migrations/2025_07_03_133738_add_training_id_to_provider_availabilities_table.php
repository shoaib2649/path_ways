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
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->unsignedBigInteger('training_id')->nullable()->after('provider_id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->dropColumn('training_id');
        });
    }
};
