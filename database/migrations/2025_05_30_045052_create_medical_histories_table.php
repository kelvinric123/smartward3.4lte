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
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['condition', 'allergy', 'surgery', 'family_history']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date_diagnosed')->nullable();
            $table->string('severity')->nullable(); // For allergies
            $table->string('status')->default('active'); // active, resolved, chronic
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};
