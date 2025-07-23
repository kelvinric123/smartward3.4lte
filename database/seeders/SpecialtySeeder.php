<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'Medical',
                'description' => 'General medical care and internal medicine',
            ],
            [
                'name' => 'Surgical',
                'description' => 'General surgery and surgical procedures',
            ],
            [
                'name' => 'Orthopedics',
                'description' => 'Specializes in musculoskeletal system disorders',
            ],
            [
                'name' => 'Paediatrics',
                'description' => 'Specializes in children\'s health and development',
            ],
        ];

        $hospitals = Hospital::all();

        foreach ($hospitals as $hospital) {
            foreach ($specialties as $specialty) {
                Specialty::create([
                    'name' => $specialty['name'],
                    'description' => $specialty['description'],
                    'hospital_id' => $hospital->id,
                    'is_active' => true,
                ]);
            }
        }
    }
} 