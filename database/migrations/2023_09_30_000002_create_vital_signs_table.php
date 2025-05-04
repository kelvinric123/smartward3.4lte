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
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('recorded_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->dateTime('recorded_at');
            
            // Basic vital signs
            $table->decimal('temperature', 5, 2)->nullable()->comment('Temperature in Celsius');
            $table->decimal('heart_rate', 5, 2)->nullable()->comment('Heart rate in BPM');
            $table->decimal('respiratory_rate', 5, 2)->nullable()->comment('Respiratory rate in breaths per minute');
            $table->decimal('systolic_bp', 5, 2)->nullable()->comment('Systolic blood pressure in mmHg');
            $table->decimal('diastolic_bp', 5, 2)->nullable()->comment('Diastolic blood pressure in mmHg');
            $table->decimal('oxygen_saturation', 5, 2)->nullable()->comment('Oxygen saturation in percentage');
            $table->text('consciousness')->nullable()->comment('AVPU scale: Alert, Verbal, Pain, Unresponsive');
            
            // Early Warning Score
            $table->integer('temperature_score')->default(0);
            $table->integer('heart_rate_score')->default(0);
            $table->integer('respiratory_rate_score')->default(0);
            $table->integer('blood_pressure_score')->default(0);
            $table->integer('oxygen_saturation_score')->default(0);
            $table->integer('consciousness_score')->default(0);
            $table->integer('total_ews')->default(0)->comment('Total Early Warning Score');
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
}; 