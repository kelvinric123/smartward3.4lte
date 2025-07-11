<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\VitalSign;
use App\Models\User;
use Carbon\Carbon;

class AishahVitalSignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create Aishah bin Ibrahim
        $patient = Patient::firstOrCreate(
            ['name' => 'Aishah bin Ibrahim'],
            [
                'identity_number' => '880526-14-5548', // Random Malaysian IC
                'identity_type' => 'ic',
                'age' => 36,
                'gender' => 'female',
                'email' => 'aishah.ibrahim@example.com',
                'phone' => '012-345-6789',
                'address' => 'Jalan Ampang, Kuala Lumpur, Malaysia',
                'mrn' => 'MRN-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT),
            ]
        );

        // Get the first admin user to be set as recorder
        $recorder = User::first();

        // Generate 30 vital signs over the past month
        $this->generateVitalSigns($patient, $recorder, 30);

        $this->command->info('Successfully created Aishah bin Ibrahim and 30 vital sign records.');
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
        // Start date (30 days ago)
        $startDate = Carbon::now()->subDays(30);
        
        // Clear existing vital signs for clean demo data
        VitalSign::where('patient_id', $patient->id)->delete();
        
        // Generate vitals with a general trend of patient recovery
        for ($i = 0; $i < $count; $i++) {
            // Calculate date for this entry - space them out over 30 days
            $recordedAt = $startDate->copy()->addHours($i * 24);
            
            // Starting values - slightly elevated
            $baseTemperature = 37.8;
            $baseHeartRate = 98;
            $baseRespiratoryRate = 24;
            $baseSystolic = 145;
            $baseDiastolic = 95;
            $baseOxygenSaturation = 93;
            
            // Recovery factor (0 to 1) - higher means closer to healthy values
            $recoveryFactor = min(1, $i / ($count * 0.8));
            
            // Add some random variation
            $randomFactor = mt_rand(-10, 10) / 100; // -0.1 to 0.1
            
            // Gradually improve vital signs with some randomness
            $temperature = round($baseTemperature - ($recoveryFactor * 1.3) + $randomFactor, 1);
            $heartRate = round($baseHeartRate - ($recoveryFactor * 20) + (mt_rand(-5, 5)), 0);
            $respiratoryRate = round($baseRespiratoryRate - ($recoveryFactor * 8) + (mt_rand(-2, 2)), 0);
            $systolicBP = round($baseSystolic - ($recoveryFactor * 25) + (mt_rand(-8, 8)), 0);
            $diastolicBP = round($baseDiastolic - ($recoveryFactor * 20) + (mt_rand(-5, 5)), 0);
            $oxygenSaturation = round(min(100, $baseOxygenSaturation + ($recoveryFactor * 5) + (mt_rand(-1, 1))), 0);
            
            // Set consciousness (mostly Alert, but some early readings may be Verbal)
            $consciousness = 'A'; // Alert
            if ($i < 3 && mt_rand(0, 1) == 1) {
                $consciousness = 'V'; // Verbal
            }
            
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
     * Generate appropriate notes based on recovery progress
     */
    private function generateNotes(int $index, int $totalCount, int $ewsScore): string
    {
        $earlyNotes = [
            'Patient admitted with hypertension and mild fever. Monitoring closely.',
            'Patient reports headache and general discomfort. Administered prescribed medication.',
            'Elevated blood pressure and respiratory rate. Continuing with prescribed treatment.',
            'Patient still exhibiting elevated vital signs but responding to treatment.',
            'Mild improvement in vital signs. Patient reports feeling slightly better.'
        ];
        
        $midNotes = [
            'Blood pressure trending downward. Patient reports improved comfort level.',
            'Continuing to see improvement in vital signs. Maintaining current treatment regimen.',
            'Patient resting comfortably. Vital signs showing steady improvement.',
            'Respiratory rate normalizing. Patient able to perform breathing exercises.',
            'Temperature within normal range. Blood pressure still slightly elevated.'
        ];
        
        $lateNotes = [
            'Most vital signs approaching normal ranges. Patient reports feeling much better.',
            'Continued improvement in all vital signs. Decreasing monitoring frequency.',
            'Patient ambulatory with minimal assistance. Vital signs stable.',
            'Blood pressure near target range. Patient engaging in light activity.',
            'All vital signs within normal parameters. Patient doing well.'
        ];
        
        // Select appropriate note based on progress
        $progress = $index / $totalCount;
        
        if ($progress < 0.3) {
            $notes = $earlyNotes[mt_rand(0, count($earlyNotes) - 1)];
        } elseif ($progress < 0.7) {
            $notes = $midNotes[mt_rand(0, count($midNotes) - 1)];
        } else {
            $notes = $lateNotes[mt_rand(0, count($lateNotes) - 1)];
        }
        
        // Add EWS-specific observation if score is high
        if ($ewsScore >= 5) {
            $notes .= ' EWS is elevated - increased monitoring per protocol.';
        } elseif ($ewsScore >= 3) {
            $notes .= ' Monitoring EWS closely.';
        }
        
        return $notes;
    }
}
