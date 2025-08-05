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
        Schema::table('providers', function (Blueprint $table) {
            $table->string('colour')->nullable()->after('id'); // or place after any column
        });
    }

    public function down()
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropColumn('colour');
        });
    }
};
