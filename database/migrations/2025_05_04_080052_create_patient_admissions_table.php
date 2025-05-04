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
        Schema::create('patient_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('ward_id')->nullable()->constrained()->nullOnDelete();
            $table->string('bed_number')->nullable();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('admission_date');
            $table->foreignId('consultant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('nurse_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->foreignId('admitted_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->text('admission_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_admissions');
    }
};
