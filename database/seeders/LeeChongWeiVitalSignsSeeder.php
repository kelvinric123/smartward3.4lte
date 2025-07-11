<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\VitalSign;
use App\Models\User;
use Carbon\Carbon;

class LeeChongWeiVitalSignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find Lee Chong Wei patient
        $patient = Patient::where('name', 'Lee Chong Wei')->first();
        
        if (!$patient) {
            $this->command->error('Patient Lee Chong Wei not found. Make sure to run PatientSeeder first.');
            return;
        }

        // Get the first admin user to be set as recorder
        $recorder = User::first();

        // Generate 20 vital signs over the past two weeks
        $this->generateVitalSigns($patient, $recorder, 20);

        $this->command->info('Successfully created 20 vital sign records for Lee Chong Wei.');
    }

    /**
     * Generate vital signs for patient
     * 
     * @param Patient $patient
     * @param User $recorder
     * @param int $count
     */
    private function generateVitalSigns(Patient $patient, User $recorder, int $count): void
    {
        // Start date (14 days ago)
        $startDate = Carbon::now()->subDays(14);
        
        // Clear existing vital signs for clean demo data
        VitalSign::where('patient_id', $patient->id)->delete();
        
        // Generate vitals with athletic baseline (Lee Chong Wei is a badminton athlete)
        for ($i = 0; $i < $count; $i++) {
            // Calculate date for this entry - space them out over 14 days
            $recordedAt = $startDate->copy()->addHours($i * 16); // Multiple readings per day
            
            // Athlete baseline values - generally lower heart rate, BP, respiratory rate
            $baseTemperature = 36.5;
            $baseHeartRate = 58; // Athletic resting heart rate
            $baseRespiratoryRate = 14;
            $baseSystolic = 110;
            $baseDiastolic = 70;
            $baseOxygenSaturation = 98;
            
            // Add some random variation
            $temperatureVariation = mt_rand(-5, 8) / 10; // -0.5 to 0.8
            $heartRateVariation = mt_rand(-3, 7); // -3 to 7
            $respiratoryRateVariation = mt_rand(-1, 2); // -1 to 2
            $systolicVariation = mt_rand(-5, 10); // -5 to 10
            $diastolicVariation = mt_rand(-3, 8); // -3 to 8
            $oxygenSaturationVariation = mt_rand(-1, 2); // -1 to 2
            
            // Apply variations
            $temperature = round($baseTemperature + $temperatureVariation, 1);
            $heartRate = round($baseHeartRate + $heartRateVariation, 0);
            $respiratoryRate = round($baseRespiratoryRate + $respiratoryRateVariation, 0);
            $systolicBP = round($baseSystolic + $systolicVariation, 0);
            $diastolicBP = round($baseDiastolic + $diastolicVariation, 0);
            $oxygenSaturation = min(100, round($baseOxygenSaturation + $oxygenSaturationVariation, 0));
            
            // Athletes typically have "Alert" consciousness
            $consciousness = 'A';
            
            // Calculate EWS scores
            $ewsResults = VitalSign::calculateEWS([
                'temperature' => $temperature,
                'heart_rate' => $heartRate,
                'respiratory_rate' => $respiratoryRate,
                'systolic_bp' => $systolicBP,
                'oxygen_saturation' => $oxygenSaturation,
                'consciousness' => $consciousness,
            ]);
            
            // Create vital sign record
            VitalSign::create([
                'patient_id' => $patient->id,
                'recorded_by' => $recorder->id,
                'recorded_at' => $recordedAt,
                'temperature' => $temperature,
                'heart_rate' => $heartRate,
                'respiratory_rate' => $respiratoryRate,
                'systolic_bp' => $systolicBP,
                'diastolic_bp' => $diastolicBP,
                'oxygen_saturation' => $oxygenSaturation,
                'consciousness' => $consciousness,
                'temperature_score' => $ewsResults['scores']['temperature_score'],
                'heart_rate_score' => $ewsResults['scores']['heart_rate_score'],
                'respiratory_rate_score' => $ewsResults['scores']['respiratory_rate_score'],
                'blood_pressure_score' => $ewsResults['scores']['blood_pressure_score'],
                'oxygen_saturation_score' => $ewsResults['scores']['oxygen_saturation_score'],
                'consciousness_score' => $ewsResults['scores']['consciousness_score'],
                'total_ews' => $ewsResults['total_ews'],
                'notes' => $this->generateNotes($i, $count, $ewsResults['total_ews']),
            ]);
        }
    }
    
    /**
     * Generate appropriate notes for an athlete
     */
    private function generateNotes(int $index, int $totalCount, int $ewsScore): string
    {
        $notes = [
            'Routine check-up. Vital signs consistent with athletic baseline.',
            'Post-training assessment. Normal recovery pattern observed.',
            'Excellent physical condition. Vital signs within athletic normal range.',
            'Resting measurements taken after light activity.',
            'Athlete reports feeling well. All parameters within expected ranges.',
            'Measurements taken during regular health monitoring.',
            'Shows characteristic athletic vital signs pattern.',
            'Vital signs reflect excellent cardiovascular conditioning.',
            'Routine health monitoring for athletic performance tracking.',
            'Patient exhibits typical elite athlete vital sign profile.'
        ];
        
        // Randomly select a note
        $randomNote = $notes[mt_rand(0, count($notes) - 1)];
        
        // Add EWS-specific observation if score is unusual for an athlete
        if ($ewsScore >= 3) {
            $randomNote .= ' Note: EWS slightly elevated by athletic normal standards - will continue monitoring.';
        }
        
        return $randomNote;
    }
} 