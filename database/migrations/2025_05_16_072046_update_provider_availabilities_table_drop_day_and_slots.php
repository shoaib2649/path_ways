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
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->dropColumn('day_of_week');
            $table->dropColumn('slots');
        });
    }

    public function down()
    {
        Schema::table('provider_availabilities', function (Blueprint $table) {
            $table->string('day_of_week')->nullable();
            $table->json('slots')->nullable();
        });
    }
};
