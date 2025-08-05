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
        Schema::table('modifiers', function (Blueprint $table) {
            $table->string('colour')->nullable()->after('fees'); // You can change 'id' to any column
        });
    }

    public function down()
    {
        Schema::table('modifiers', function (Blueprint $table) {
            $table->dropColumn('colour');
        });
    }
};
