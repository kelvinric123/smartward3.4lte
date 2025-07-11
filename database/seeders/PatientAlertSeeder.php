<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PatientAlert;
use App\Models\Patient;
use App\Models\Ward;
use App\Models\Bed;
use Faker\Factory as Faker;

class PatientAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get all wards with beds that have patients
        $bedsWithPatients = Bed::where('status', 'occupied')
            ->whereNotNull('patient_id')
            ->with(['patient', 'ward'])
            ->get();
        
        if ($bedsWithPatients->isEmpty()) {
            $this->command->info('No beds with patients found. Skipping alert seeding.');
            return;
        }
        
        $alertTypes = ['emergency', 'pain', 'assistance', 'water', 'bathroom', 'food'];
        $alertMessages = [
            'emergency' => [
                'Patient experiencing chest pain!',
                'Patient has fallen and cannot get up!',
                'Patient is having difficulty breathing!',
                'Patient appears to be having an allergic reaction!',
            ],
            'pain' => [
                'Patient requesting pain medication.',
                'Patient reporting severe pain level 8/10.',
                'Patient complaining of headache.',
                'Patient experiencing abdominal pain.',
            ],
            'assistance' => [
                'Patient needs help getting to the bathroom.',
                'Patient requests assistance with mobility.',
                'Patient needs help adjusting bed position.',
                'Patient requires assistance with personal care.',
            ],
            'water' => [
                'Patient requesting water.',
                'Patient needs fresh water pitcher.',
                'Patient asking for ice water.',
                'Patient needs help with drinking.',
            ],
            'bathroom' => [
                'Patient needs to use the restroom urgently.',
                'Patient requesting bedpan assistance.',
                'Patient needs bathroom escort.',
                'Patient experiencing bathroom emergency.',
            ],
            'food' => [
                'Patient requesting meal assistance.',
                'Patient needs help with eating.',
                'Patient asking about meal schedule.',
                'Patient has dietary concerns.',
            ],
        ];
        
        // Create 15-20 test alerts
        foreach ($bedsWithPatients->take(10) as $bed) {
            // Create 1-3 alerts per bed with patient
            $alertCount = $faker->numberBetween(1, 3);
            
            for ($i = 0; $i < $alertCount; $i++) {
                $alertType = $faker->randomElement($alertTypes);
                $message = $faker->randomElement($alertMessages[$alertType]);
                $status = $faker->randomElement(['new', 'seen', 'resolved'], [60, 30, 10]); // More new alerts
                $isUrgent = in_array($alertType, ['emergency', 'pain']) ? $faker->boolean(80) : $faker->boolean(20);
                
                PatientAlert::create([
                    'patient_id' => $bed->patient_id,
                    'ward_id' => $bed->ward_id,
                    'bed_id' => $bed->id,
                    'alert_type' => $alertType,
                    'message' => $message,
                    'status' => $status,
                    'is_urgent' => $isUrgent,
                    'created_at' => $faker->dateTimeBetween('-2 hours', 'now'),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('Patient alerts seeded successfully.');
    }
} 