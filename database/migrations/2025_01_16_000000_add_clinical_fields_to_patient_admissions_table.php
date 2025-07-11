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
            // Diet Type - HL7 Standard dietary requirements
            $table->enum('diet_type', [
                'REG', // Regular
                'NPO', // Nothing by mouth
                'CLF', // Clear liquid
                'FLF', // Full liquid
                'LCH', // Low cholesterol
                'LCS', // Low calorie
                'LNS', // Low sodium
                'DBT', // Diabetic
                'VEG', // Vegetarian
                'VGN', // Vegan
                'HSL', // Halal
                'KSH', // Kosher
                'RST', // Renal/dialysis
                'CAR', // Cardiac
                'SFT', // Soft
                'BLD', // Bland
                'PUR'  // Pureed
            ])->nullable()->after('risk_factors')->comment('Dietary requirements - HL7 standard codes');
            
            // Patient Class - HL7 PV1.2 field
            $table->enum('patient_class', [
                'I',  // Inpatient
                'O',  // Outpatient
                'A',  // Ambulatory
                'E',  // Emergency
                'N',  // Not applicable
                'R',  // Recurring patient
                'B',  // Obstetrics
                'C',  // Commercial Account
                'U'   // Unknown
            ])->nullable()->after('diet_type')->comment('Patient class - HL7 PV1.2 standard');
            
            // Expected Discharge Date
            $table->dateTime('expected_discharge_date')->nullable()->after('patient_class')->comment('Expected discharge date and time');
            
            // Expected Length of Stay (in days)
            $table->integer('expected_length_of_stay')->nullable()->after('expected_discharge_date')->comment('Expected length of stay in days');
            
            // Fall Risk Alert - ZAT code
            $table->enum('fall_risk_alert', [
                'NO',  // No risk
                'LOW', // Low risk
                'MOD', // Moderate risk
                'HIGH', // High risk
                'FR'   // Fall risk alert active
            ])->default('NO')->after('expected_length_of_stay')->comment('Fall risk assessment - ZAT code');
            
            // Isolation Precautions - ZIT code
            $table->enum('isolation_precautions', [
                'NONE',    // No isolation
                'STD',     // Standard precautions
                'CON',     // Contact precautions
                'DROP',    // Droplet precautions
                'AIR',     // Airborne precautions
                'DAC',     // Droplet, Airborne, Contact (all three)
                'DC',      // Droplet and Contact
                'AC',      // Airborne and Contact
                'AD'       // Airborne and Droplet
            ])->default('NONE')->after('fall_risk_alert')->comment('Isolation precautions - ZIT code');
            
            // Additional clinical notes
            $table->text('clinical_alerts')->nullable()->after('isolation_precautions')->comment('Additional clinical alerts and notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_admissions', function (Blueprint $table) {
            $table->dropColumn([
                'diet_type',
                'patient_class',
                'expected_discharge_date',
                'expected_length_of_stay',
                'fall_risk_alert',
                'isolation_precautions',
                'clinical_alerts'
            ]);
        });
    }
}; 