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
        Schema::create('chat_conversation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id'); // Referencia a la tabla users
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_conversation_user');
    }
};
