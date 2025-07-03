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
        Schema::table('vital_signs', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('vital_signs', 'gcs_total')) {
                $table->integer('gcs_total')->nullable()->comment('Glasgow Coma Scale total score (3-15)');
            }
            if (!Schema::hasColumn('vital_signs', 'gcs_eye')) {
                $table->integer('gcs_eye')->nullable()->comment('GCS Eye opening response (1-4)');
            }
            if (!Schema::hasColumn('vital_signs', 'gcs_verbal')) {
                $table->integer('gcs_verbal')->nullable()->comment('GCS Verbal response (1-5)');
            }
            if (!Schema::hasColumn('vital_signs', 'gcs_motor')) {
                $table->integer('gcs_motor')->nullable()->comment('GCS Motor response (1-6)');
            }
            
            // GCS scoring for EWS (if needed in future)
            if (!Schema::hasColumn('vital_signs', 'gcs_score')) {
                $table->integer('gcs_score')->default(0)->comment('GCS contribution to EWS if applicable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->dropColumn([
                'gcs_total',
                'gcs_eye',
                'gcs_verbal',
                'gcs_motor',
                'gcs_score'
            ]);
        });
    }
}; 