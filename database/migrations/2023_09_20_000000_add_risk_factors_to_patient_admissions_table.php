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
        Schema::table('patient_admissions', function (Blueprint $table) {
            $table->json('risk_factors')->nullable()->after('admission_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_admissions', function (Blueprint $table) {
            $table->dropColumn('risk_factors');
        });
    }
}; 