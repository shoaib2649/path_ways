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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('memberId')->nullable();
            $table->string('insurance_payer')->nullable();
            $table->string('groupId')->nullable();
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['memberId', 'insurance_payer', 'groupId']);
        });
    }
};
