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

            $table->dropForeign(['provider_id']);
            $table->boolean('patient_add_from_spruce')->default(false)->after('type');
            $table->unsignedBigInteger('provider_id')->nullable()->change();
            
            $table->foreign('provider_id')->references('id')->on('providers')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
            $table->dropColumn('patient_add_from_spruce');
            $table->unsignedBigInteger('provider_id')->nullable(false)->change();
            $table->foreign('provider_id') ->references('id')->on('providers')->cascadeOnDelete();
        });
    }
};
