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
        Schema::create('spruce_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained(); // No cascade delete
            $table->string('conversation_id')->nullable();
            $table->string('conversation_item_id')->unique()->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->timestamp('lastMessageAt')->nullable();
            $table->text('note_text')->nullable();
            $table->string('author_name')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spruce_notes');
    }
};
