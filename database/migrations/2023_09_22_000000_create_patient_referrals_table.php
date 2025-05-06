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
        Schema::create('patient_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('admission_id')->nullable()->references('id')->on('patient_admissions')->nullOnDelete();
            $table->foreignId('from_ward_id')->nullable()->constrained('wards')->nullOnDelete();
            $table->foreignId('from_consultant_id')->nullable()->constrained('consultants')->nullOnDelete();
            $table->foreignId('to_specialty_id')->nullable()->constrained('specialties')->nullOnDelete();
            $table->foreignId('to_consultant_id')->nullable()->constrained('consultants')->nullOnDelete();
            $table->string('to_specialty')->nullable();
            $table->string('to_consultant')->nullable();
            $table->string('clinical_question')->nullable();
            $table->enum('urgency', ['routine', 'urgent', 'emergency'])->default('routine');
            $table->string('referring_doctor')->nullable();
            $table->dateTime('referral_date');
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined', 'completed'])->default('pending');
            $table->foreignId('referred_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->text('response_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_referrals');
    }
}; 