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
        Schema::create('hl7_message_logs', function (Blueprint $table) {
            $table->id();
            
            // Message identification
            $table->string('message_id')->unique()->comment('Unique message identifier');
            $table->string('message_control_id')->nullable()->comment('HL7 message control ID from MSH segment');
            $table->string('message_type', 50)->default('ADT^A01')->comment('HL7 message type');
            
            // Message content
            $table->longText('raw_message')->comment('Raw HL7 message received');
            $table->json('headers')->nullable()->comment('HTTP headers from the request');
            $table->json('parsed_data')->nullable()->comment('Parsed HL7 message data');
            $table->json('mapped_data')->nullable()->comment('Mapped data for admission');
            
            // Processing status
            $table->enum('status', ['received', 'parsed', 'mapped', 'processed', 'failed'])
                ->default('received')
                ->comment('Processing status');
            $table->text('error_message')->nullable()->comment('Error message if processing failed');
            
            // Timestamps
            $table->timestamp('received_at')->comment('When the message was received');
            $table->timestamp('processed_at')->nullable()->comment('When parsing/mapping started');
            $table->timestamp('completed_at')->nullable()->comment('When processing completed');
            
            // Processing metrics
            $table->integer('processing_time_ms')->nullable()->comment('Processing time in milliseconds');
            
            // Related records
            $table->foreignId('admission_id')->nullable()->constrained('patient_admissions')->nullOnDelete()->comment('Created admission record');
            $table->string('patient_mrn', 50)->nullable()->comment('Patient MRN from message');
            $table->string('patient_name')->nullable()->comment('Patient name from message');
            
            // System information
            $table->string('source_system', 100)->nullable()->comment('Source system that sent the message');
            $table->string('destination_system', 100)->nullable()->comment('Destination system (our system)');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'received_at']);
            $table->index(['message_type', 'received_at']);
            $table->index(['patient_mrn', 'received_at']);
            $table->index(['message_control_id']);
            $table->index(['received_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hl7_message_logs');
    }
};
