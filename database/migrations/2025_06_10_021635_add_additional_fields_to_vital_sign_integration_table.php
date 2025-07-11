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
            $table->decimal('temperature', 5, 2)->nullable()->after('pulse_rate')->comment('Temperature in Celsius');
            $table->integer('mean_bp')->nullable()->after('diastolic_bp')->comment('Mean blood pressure in mmHg');
            $table->string('body_position', 50)->nullable()->after('ews_score_total')->comment('Body position during measurement');
            $table->decimal('height', 5, 2)->nullable()->after('body_position')->comment('Height in cm');
            $table->decimal('weight', 5, 2)->nullable()->after('height')->comment('Weight in kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vital_sign_integration', function (Blueprint $table) {
            $table->dropColumn(['temperature', 'mean_bp', 'body_position', 'height', 'weight']);
        });
    }
};
