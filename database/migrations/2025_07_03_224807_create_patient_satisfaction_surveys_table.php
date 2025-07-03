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
        Schema::create('patient_satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('ward_id')->nullable()->constrained('wards')->onDelete('set null');
            $table->integer('care_rating')->nullable(); // 1-5 stars for care quality
            $table->integer('staff_rating')->nullable(); // 1-5 stars for staff responsiveness
            $table->integer('clean_rating')->nullable(); // 1-5 stars for cleanliness
            $table->integer('comm_rating')->nullable(); // 1-5 stars for communication
            $table->text('comments')->nullable(); // Additional comments
            $table->decimal('overall_rating', 3, 2)->nullable(); // Calculated average rating
            $table->enum('response_type', ['positive', 'neutral', 'negative'])->nullable(); // Based on overall rating
            $table->string('category', 50)->default('general'); // Category for analytics
            $table->timestamps();
            
            $table->index(['patient_id', 'created_at']);
            $table->index(['ward_id', 'created_at']);
            $table->index(['response_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_satisfaction_surveys');
    }
};
