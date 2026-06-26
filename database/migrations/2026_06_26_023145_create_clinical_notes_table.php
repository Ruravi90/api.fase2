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
        Schema::create('clinical_notes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('medical_record_id')->unsigned();
            
            $table->integer('schedule_id')->unsigned();
            
            $table->integer('doctor_id')->unsigned();
            
            // Campos SOAP (Sensitive)
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('analysis')->nullable();
            $table->text('plan')->nullable();
            
            // Signos Vitales
            $table->decimal('weight', 5, 2)->nullable(); // kg
            $table->string('blood_pressure', 20)->nullable(); // Ej: 120/80
            $table->decimal('temperature', 4, 2)->nullable(); // °C
            $table->integer('heart_rate')->nullable(); // lpm
            $table->integer('respiratory_rate')->nullable(); // rpm
            $table->integer('oxygen_saturation')->nullable(); // %
            
            // Status y Auditoría (NOM-024)
            $table->enum('status', ['draft', 'signed'])->default('draft');
            $table->timestamp('signed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_notes');
    }
};
