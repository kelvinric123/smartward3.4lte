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
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->string('bed_number');
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->foreignId('ward_id')->constrained()->onDelete('cascade');
            $table->foreignId('consultant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('nurse_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Create a unique constraint for bed_number within the same ward
            $table->unique(['ward_id', 'bed_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};
