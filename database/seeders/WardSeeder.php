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
        // Get Qmed hospital 1
        $hospital = Hospital::where('name', 'Qmed hospital 1')->first();
        
        if (!$hospital) {
            $this->command->info('Qmed hospital 1 not found. Skipping ward creation.');
            return;
        }

        // Get all specialties for this hospital
        $specialties = Specialty::where('hospital_id', $hospital->id)->get();
        
        foreach ($specialties as $specialty) {
            // Create two wards for each specialty
            Ward::create([
                'name' => $specialty->name . ' Ward A',
                'description' => 'Primary ward for ' . $specialty->name . ' department',
                'capacity' => 20,
                'hospital_id' => $hospital->id,
                'specialty_id' => $specialty->id,
                'is_active' => true,
            ]);
            
            Ward::create([
                'name' => $specialty->name . ' Ward B',
                'description' => 'Secondary ward for ' . $specialty->name . ' department',
                'capacity' => 15,
                'hospital_id' => $hospital->id,
                'specialty_id' => $specialty->id,
                'is_active' => true,
            ]);
        }
    }
}
