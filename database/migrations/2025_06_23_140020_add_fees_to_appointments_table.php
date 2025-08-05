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
        Schema::table('appointments', function (Blueprint $table) {
            $table->decimal('fees', 8, 2)->nullable()->after('cpt_code'); // replace column_name with the column after which you want to add `fees`
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('fees');
        });
    }
};
