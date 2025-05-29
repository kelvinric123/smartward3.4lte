<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medication;
use App\Models\MedicalHistory;
use App\Models\Patient;

class MedicalInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if patient with ID 7 exists
        $patient = Patient::find(7);
        
        if (!$patient) {
            $this->command->warn('Patient with ID 7 not found. Skipping medical information seeder.');
            return;
        }
        
        // Clear existing data for patient 7
        Medication::where('patient_id', 7)->delete();
        MedicalHistory::where('patient_id', 7)->delete();
        
        // Add medications for patient 7
        $medications = [
            [
                'patient_id' => 7,
                'medication_name' => 'Paracetamol',
                'dosage' => '500mg',
                'frequency' => 'Every 6 hours',
                'route' => 'Oral',
                'instructions' => 'Take with food to avoid stomach upset',
                'start_date' => now()->subDays(5),
                'prescribed_by' => 'Dr. Sarah Wilson',
                'status' => 'active',
                'notes' => 'For pain and fever management',
            ],
            [
                'patient_id' => 7,
                'medication_name' => 'Amoxicillin',
                'dosage' => '250mg',
                'frequency' => 'Three times daily',
                'route' => 'Oral',
                'instructions' => 'Complete the full course even if feeling better',
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(4),
                'prescribed_by' => 'Dr. Sarah Wilson',
                'status' => 'active',
                'notes' => 'Antibiotic for bacterial infection',
            ],
            [
                'patient_id' => 7,
                'medication_name' => 'Omeprazole',
                'dosage' => '20mg',
                'frequency' => 'Once daily',
                'route' => 'Oral',
                'instructions' => 'Take 30 minutes before breakfast',
                'start_date' => now()->subDays(10),
                'prescribed_by' => 'Dr. Sarah Wilson',
                'status' => 'active',
                'notes' => 'For acid reflux protection',
            ],
            [
                'patient_id' => 7,
                'medication_name' => 'Aspirin',
                'dosage' => '75mg',
                'frequency' => 'Once daily',
                'route' => 'Oral',
                'instructions' => 'Take with food',
                'start_date' => now()->subMonths(6),
                'prescribed_by' => 'Dr. Sarah Wilson',
                'status' => 'discontinued',
                'notes' => 'Discontinued due to gastric irritation',
            ],
        ];
        
        foreach ($medications as $medication) {
            Medication::create($medication);
        }
        
        // Add medical histories for patient 7
        $medicalHistories = [
            [
                'patient_id' => 7,
                'type' => 'condition',
                'title' => 'Hypertension',
                'description' => 'High blood pressure diagnosed during routine check-up',
                'date_diagnosed' => now()->subYear(),
                'status' => 'chronic',
                'notes' => 'Well controlled with medication and lifestyle changes',
            ],
            [
                'patient_id' => 7,
                'type' => 'allergy',
                'title' => 'Penicillin Allergy',
                'description' => 'Severe allergic reaction to penicillin-based antibiotics',
                'date_diagnosed' => now()->subYears(5),
                'severity' => 'severe',
                'status' => 'active',
                'notes' => 'Patient develops rash and difficulty breathing. Avoid all penicillin-based drugs.',
            ],
            [
                'patient_id' => 7,
                'type' => 'surgery',
                'title' => 'Appendectomy',
                'description' => 'Surgical removal of appendix due to acute appendicitis',
                'date_diagnosed' => now()->subYears(3),
                'status' => 'resolved',
                'notes' => 'Uncomplicated laparoscopic procedure. Full recovery.',
            ],
            [
                'patient_id' => 7,
                'type' => 'condition',
                'title' => 'Type 2 Diabetes',
                'description' => 'Non-insulin dependent diabetes mellitus',
                'date_diagnosed' => now()->subYears(2),
                'status' => 'chronic',
                'notes' => 'Managed with diet, exercise, and oral medication',
            ],
            [
                'patient_id' => 7,
                'type' => 'family_history',
                'title' => 'Heart Disease',
                'description' => 'Father had myocardial infarction at age 55',
                'status' => 'active',
                'notes' => 'Regular cardiac screening recommended',
            ],
            [
                'patient_id' => 7,
                'type' => 'allergy',
                'title' => 'Shellfish Allergy',
                'description' => 'Allergic reaction to shellfish including prawns, crabs, and lobsters',
                'date_diagnosed' => now()->subYears(10),
                'severity' => 'moderate',
                'status' => 'active',
                'notes' => 'Causes hives and nausea. Carries EpiPen.',
            ],
        ];
        
        foreach ($medicalHistories as $history) {
            MedicalHistory::create($history);
        }
        
        $this->command->info('Medical information seeded successfully for patient ID 7.');
    }
}
