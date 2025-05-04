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
        Schema::create('patient_discharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('ward_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bed_number')->nullable();
            $table->dateTime('discharge_date');
            $table->string('discharge_type')->default('routine'); // routine, against_medical_advice, deceased, etc.
            $table->foreignId('discharged_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->text('discharge_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_discharges');
    }
};
