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
        Schema::table('daily_availability_summaries', function (Blueprint $table) {
            $table->integer('therapy_patients')->nullable()->default(null)->change();
            $table->integer('assessment_patients')->nullable()->default(null)->change();
        });
    }

    public function down()
    {
        Schema::table('daily_availability_summaries', function (Blueprint $table) {
            $table->integer('therapy_patients')->default(0)->change();
            $table->integer('assessment_patients')->default(0)->change();
        });
    }
};
