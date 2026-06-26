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
        Schema::create('turn_queues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->string('turn_number'); // e.g., 'A-1', 'B-1'
            $table->enum('status', ['waiting', 'in_progress', 'completed', 'delayed'])->default('waiting');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->foreign('schedule_id')->references('id')->on('schedule')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turn_queues');
    }
};
