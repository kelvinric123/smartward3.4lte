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
        
        // Generate beds for each ward
        foreach ($wards as $ward) {
            $bedCount = min($ward->capacity, 20); // Limit to maximum 20 beds per ward
            
            for ($i = 1; $i <= $bedCount; $i++) {
                // All beds start as available
                $status = Bed::STATUS_AVAILABLE;
                
                // Create the bed
                $bed = Bed::create([
                    'bed_number' => 'B' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'status' => $status,
                    'ward_id' => $ward->id,
                    'consultant_id' => null,
                    'nurse_id' => null,
                    'patient_id' => null,
                    'notes' => null,
                    'is_active' => true,
                ]);
                
                $this->command->info("Created bed: {$bed->bed_number} in {$ward->name} at {$ward->hospital->name}");
            }
        }
    }
}
