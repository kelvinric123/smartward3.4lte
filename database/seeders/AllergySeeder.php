<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\MedicalHistory;

class AllergySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some existing patients to add allergies to
        $patients = Patient::limit(10)->get();
        
        if ($patients->isEmpty()) {
            $this->command->warn('No patients found. Please run PatientSeeder first.');
            return;
        }
        
        $commonAllergies = [
            ['title' => 'Penicillin', 'severity' => 'severe', 'description' => 'Severe allergic reaction to penicillin antibiotics'],
            ['title' => 'Sulfa drugs', 'severity' => 'moderate', 'description' => 'Moderate reaction to sulfonamide medications'],
            ['title' => 'Peanuts', 'severity' => 'severe', 'description' => 'Anaphylactic reaction to peanuts'],
            ['title' => 'Shellfish', 'severity' => 'moderate', 'description' => 'Allergic reaction to shellfish'],
            ['title' => 'Latex', 'severity' => 'mild', 'description' => 'Contact dermatitis from latex exposure'],
            ['title' => 'Iodine contrast', 'severity' => 'severe', 'description' => 'Severe reaction to iodinated contrast media'],
            ['title' => 'Aspirin', 'severity' => 'moderate', 'description' => 'Gastrointestinal reaction to aspirin'],
            ['title' => 'Morphine', 'severity' => 'mild', 'description' => 'Nausea and vomiting with morphine'],
            ['title' => 'Codeine', 'severity' => 'moderate', 'description' => 'Respiratory depression with codeine'],
            ['title' => 'Bee stings', 'severity' => 'severe', 'description' => 'Anaphylactic reaction to bee venom'],
            ['title' => 'Tree nuts', 'severity' => 'severe', 'description' => 'Severe allergic reaction to tree nuts'],
            ['title' => 'Eggs', 'severity' => 'mild', 'description' => 'Mild gastrointestinal reaction to eggs'],
            ['title' => 'Milk', 'severity' => 'mild', 'description' => 'Lactose intolerance and mild allergic reaction'],
            ['title' => 'Soy', 'severity' => 'moderate', 'description' => 'Moderate allergic reaction to soy products'],
            ['title' => 'Wheat', 'severity' => 'moderate', 'description' => 'Celiac disease and wheat allergy'],
        ];
        
        $this->command->info('Seeding allergies for patients...');
        
        foreach ($patients as $patient) {
            // Randomly assign 0-3 allergies per patient
            $allergyCount = rand(0, 3);
            
            if ($allergyCount > 0) {
                $selectedAllergies = collect($commonAllergies)->random($allergyCount);
                
                foreach ($selectedAllergies as $allergy) {
                    MedicalHistory::create([
                        'patient_id' => $patient->id,
                        'type' => 'allergy',
                        'title' => $allergy['title'],
                        'description' => $allergy['description'],
                        'severity' => $allergy['severity'],
                        'status' => 'active',
                        'date_diagnosed' => now()->subDays(rand(30, 365)),
                        'notes' => 'Patient reported allergy during admission assessment',
                    ]);
                }
                
                $this->command->info("Added {$allergyCount} allergies for patient: {$patient->name}");
            }
        }
        
        $this->command->info('Allergy seeding completed!');
    }
} 