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
                'name' => 'Cardiology',
                'description' => 'Specializes in heart and cardiovascular system disorders',
            ],
            [
                'name' => 'Neurology',
                'description' => 'Specializes in disorders of the nervous system',
            ],
            [
                'name' => 'Orthopedics',
                'description' => 'Specializes in musculoskeletal system disorders',
            ],
            [
                'name' => 'Pediatrics',
                'description' => 'Specializes in children\'s health and development',
            ],
            [
                'name' => 'Obstetrics & Gynecology',
                'description' => 'Specializes in women\'s health and pregnancy',
            ],
            [
                'name' => 'Dermatology',
                'description' => 'Specializes in skin disorders and diseases',
            ],
            [
                'name' => 'Ophthalmology',
                'description' => 'Specializes in eye disorders and vision care',
            ],
            [
                'name' => 'ENT (Ear, Nose & Throat)',
                'description' => 'Specializes in disorders of the ear, nose, and throat',
            ],
            [
                'name' => 'Gastroenterology',
                'description' => 'Specializes in digestive system disorders',
            ],
            [
                'name' => 'Urology',
                'description' => 'Specializes in urinary system disorders',
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