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
        Schema::table('vital_sign_integration', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('vital_sign_integration', 'gcs_total')) {
                $table->integer('gcs_total')->nullable()->comment('Glasgow Coma Scale total score (3-15)');
            }
            if (!Schema::hasColumn('vital_sign_integration', 'gcs_eye')) {
                $table->integer('gcs_eye')->nullable()->comment('GCS Eye opening response (1-4)');
            }
            if (!Schema::hasColumn('vital_sign_integration', 'gcs_verbal')) {
                $table->integer('gcs_verbal')->nullable()->comment('GCS Verbal response (1-5)');
            }
            if (!Schema::hasColumn('vital_sign_integration', 'gcs_motor')) {
                $table->integer('gcs_motor')->nullable()->comment('GCS Motor response (1-6)');
            }
            
            // Additional fields that might be useful - only add if they don't exist
            if (!Schema::hasColumn('vital_sign_integration', 'temperature')) {
                $table->decimal('temperature', 5, 2)->nullable()->comment('Temperature in Celsius');
            }
            if (!Schema::hasColumn('vital_sign_integration', 'mean_bp')) {
                $table->decimal('mean_bp', 5, 2)->nullable()->comment('Mean blood pressure in mmHg');
            }
            if (!Schema::hasColumn('vital_sign_integration', 'body_position')) {
                $table->string('body_position', 50)->nullable()->comment('Patient body position');
            }
            if (!Schema::hasColumn('vital_sign_integration', 'height')) {
                $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
            }
            if (!Schema::hasColumn('vital_sign_integration', 'weight')) {
                $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kg');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vital_sign_integration', function (Blueprint $table) {
            $table->dropColumn([
                'gcs_total',
                'gcs_eye',
                'gcs_verbal',
                'gcs_motor',
                'temperature',
                'mean_bp',
                'body_position',
                'height',
                'weight'
            ]);
        });
    }
}; 