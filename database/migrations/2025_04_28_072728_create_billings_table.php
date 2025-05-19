<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();  
            $table->unsignedBigInteger('appointment_id')->nullable();  
            $table->unsignedBigInteger('patient_id')->nullable();  
            $table->unsignedBigInteger('provider_id')->nullable(); 
            $table->string('meeting_type')->nullable(); 
            $table->unsignedInteger('time')->nullable(); 
            $table->decimal('amount', 8, 2)->nullable(); 
            $table->decimal('rate', 8, 2)->nullable(); 
            $table->timestamps(); 

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade')->nullable();
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade')->nullable();
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billings');
    }
};

