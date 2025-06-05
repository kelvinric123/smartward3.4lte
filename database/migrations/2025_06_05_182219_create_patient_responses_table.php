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
        Schema::create('patient_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_alert_id')->constrained('patient_alerts')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('nurse_id')->nullable()->constrained('users')->onDelete('set null'); // User who responded
            $table->text('response_message')->nullable();
            $table->enum('status', ['sent', 'read'])->default('sent');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_responses');
    }
};
