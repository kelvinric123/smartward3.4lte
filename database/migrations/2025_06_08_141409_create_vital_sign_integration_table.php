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
        Schema::create('vital_sign_integration', function (Blueprint $table) {
            $table->id();
            
            // Timestamp from the vital signs device
            $table->timestamp('device_timestamp')->nullable()->comment('Timestamp from the device/message');
            
            // Raw HL7 message data
            $table->longText('raw_message')->nullable()->comment('Raw HL7 message received from device');
            
            // Patient information
            $table->string('mrn', 50)->nullable()->comment('Medical Record Number');
            $table->string('patient_name', 255)->nullable()->comment('Patient full name');
            
            // Vital signs data
            $table->decimal('respiratory_rate', 5, 2)->nullable()->comment('Respiratory rate in breaths per minute');
            $table->decimal('spo2', 5, 2)->nullable()->comment('Oxygen saturation percentage');
            $table->decimal('pulse_rate', 5, 2)->nullable()->comment('Pulse rate in BPM');
            $table->decimal('systolic_bp', 5, 2)->nullable()->comment('Systolic blood pressure in mmHg');
            $table->decimal('diastolic_bp', 5, 2)->nullable()->comment('Diastolic blood pressure in mmHg');
            
            // Additional vital signs data
            $table->string('avpu', 50)->nullable()->comment('AVPU consciousness level (alert, reacting to voice, reacting to pain, unresponsive)');
            $table->integer('ews_score_total')->nullable()->comment('Early Warning Score total');
            
            // Staff information
            $table->string('nurse_id', 50)->nullable()->comment('ID of the nurse/staff member recording the vitals');
            
            // Integration status fields
            $table->boolean('processed')->default(false)->comment('Whether this record has been processed/integrated');
            $table->timestamp('processed_at')->nullable()->comment('When this record was processed');
            $table->text('processing_notes')->nullable()->comment('Notes about processing or any errors');
            
            // Foreign key to link with existing patient records (optional)
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete()->comment('Link to existing patient record');
            
            // Indexes for better performance
            $table->index('mrn');
            $table->index('device_timestamp');
            $table->index('processed');
            $table->index('patient_id');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_sign_integration');
    }
};
