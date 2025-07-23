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
        Schema::create('ward_specialty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['ward_id', 'specialty_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ward_specialty');
    }
}; 