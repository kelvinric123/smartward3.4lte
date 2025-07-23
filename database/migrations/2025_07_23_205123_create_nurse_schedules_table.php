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
        Schema::create('nurse_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->json('schedule_data');
            $table->string('original_filename');
            $table->date('schedule_start_date')->nullable();
            $table->date('schedule_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('ward_id')->references('id')->on('wards')->onDelete('cascade');
            $table->index(['ward_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_schedules');
    }
};
