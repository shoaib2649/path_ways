<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProviderAvailabilityServiceTable extends Migration
{
    public function up()
    {
        Schema::create('provider_availability_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_availability_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('provider_availability_service');
    }
}
