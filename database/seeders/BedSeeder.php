<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Consultant;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all wards
        $wards = Ward::all();
        
        if ($wards->isEmpty()) {
            $this->command->info('No wards found. Skipping bed creation.');
            return;
        }
        
        // Get consultants and patients for assignments
        $consultants = Consultant::all();
        $patients = Patient::all();
        $nurses = User::whereHas('roles', function ($query) {
            $query->where('name', 'nurse');
        })->get();
        
        // Generate beds for each ward
        foreach ($wards as $ward) {
            $bedCount = min($ward->capacity, 20); // Limit to maximum 20 beds per ward
            
            for ($i = 1; $i <= $bedCount; $i++) {
                // Determine bed status - all beds available
                $status = Bed::STATUS_AVAILABLE;
                
                // Assign consultant and nurse based on status (no patients)
                $consultantId = null;
                $nurseId = null;
                $patientId = null; // Always null as no patients are admitted
                
                // Create the bed
                Bed::create([
                    'bed_number' => 'B' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'status' => $status,
                    'ward_id' => $ward->id,
                    'consultant_id' => $consultantId,
                    'nurse_id' => $nurseId,
                    'patient_id' => $patientId,
                    'notes' => null,
                    'is_active' => true,
                ]);
            }
        }
    }
    
    /**
     * Get a random element with weighted probabilities
     * 
     * @param array $options Array of options
     * @param array $weights Array of weights for each option
     * @return mixed A randomly selected option based on weights
     */
    private function getRandomWeighted(array $options, array $weights): mixed
    {
        $totalWeight = array_sum($weights);
        $randomNumber = mt_rand(1, $totalWeight);
        
        $weightSum = 0;
        foreach ($options as $index => $option) {
            $weightSum += $weights[$index];
            if ($randomNumber <= $weightSum) {
                return $option;
            }
        }
        
        return $options[0]; // Fallback to first option
    }
}
