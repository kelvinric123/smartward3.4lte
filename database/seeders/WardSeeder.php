<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all hospitals
        $hospitals = Hospital::all();
        
        if ($hospitals->isEmpty()) {
            $this->command->info('No hospitals found. Skipping ward creation.');
            return;
        }

        foreach ($hospitals as $hospital) {
            // Get all specialties for this hospital
            $specialties = Specialty::where('hospital_id', $hospital->id)->get();
            
            foreach ($specialties as $specialty) {
                // Create one ward for each specialty
                $ward = Ward::create([
                    'name' => $specialty->name . ' Ward',
                    'description' => 'Ward for ' . $specialty->name . ' department',
                    'capacity' => 20,
                    'hospital_id' => $hospital->id,
                    'specialty_id' => $specialty->id,
                    'is_active' => true,
                ]);
                
                // Attach the specialty to the ward using the new many-to-many relationship
                $ward->specialties()->attach($specialty->id);
                
                $this->command->info("Created ward: {$ward->name} at {$hospital->name}");
            }
        }
    }
}
