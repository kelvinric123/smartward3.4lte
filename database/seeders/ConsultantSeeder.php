<?php

namespace Database\Seeders;

use App\Models\Consultant;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class ConsultantSeeder extends Seeder
{
    public function run(): void
    {
        $consultants = [
            [
                'name' => 'Dr. Ahmad bin Abdullah',
                'email' => 'ahmad.abdullah',
                'phone' => '0123456789',
                'qualification' => 'MBBS (UM), MRCP (UK), FRCP (Edin)',
                'experience_years' => 15,
                'bio' => 'Senior Consultant Cardiologist with extensive experience in interventional cardiology.',
            ],
            [
                'name' => 'Dr. Siti binti Ismail',
                'email' => 'siti.ismail',
                'phone' => '0123456790',
                'qualification' => 'MBBS (UM), MD (Neurology), FRCP (Lond)',
                'experience_years' => 12,
                'bio' => 'Neurology specialist with expertise in stroke management and neurophysiology.',
            ],
            [
                'name' => 'Dr. Rajesh a/l Kumar',
                'email' => 'rajesh.kumar',
                'phone' => '0123456791',
                'qualification' => 'MBBS (UM), MS (Ortho), FRCS (Edin)',
                'experience_years' => 10,
                'bio' => 'Orthopedic surgeon specializing in joint replacement and sports injuries.',
            ],
            [
                'name' => 'Dr. Wong Mei Ling',
                'email' => 'wong.meiling',
                'phone' => '0123456792',
                'qualification' => 'MBBS (UM), MD (Pediatrics), FRCPCH (UK)',
                'experience_years' => 8,
                'bio' => 'Pediatrician with special interest in developmental pediatrics.',
            ],
            [
                'name' => 'Dr. Nor Azizah binti Omar',
                'email' => 'nor.azizah',
                'phone' => '0123456793',
                'qualification' => 'MBBS (UM), MD (O&G), FRCOG (UK)',
                'experience_years' => 14,
                'bio' => 'Obstetrician and Gynecologist with expertise in high-risk pregnancies.',
            ],
        ];

        $specialties = Specialty::all();
        $counter = 1;

        foreach ($specialties as $specialty) {
            foreach ($consultants as $consultant) {
                $hospitalName = str_replace(' ', '', $specialty->hospital->name);
                Consultant::create([
                    'name' => $consultant['name'],
                    'email' => $consultant['email'] . '.' . $counter . '@' . $hospitalName . '.com',
                    'phone' => $consultant['phone'],
                    'qualification' => $consultant['qualification'],
                    'experience_years' => $consultant['experience_years'],
                    'specialty_id' => $specialty->id,
                    'bio' => $consultant['bio'],
                    'is_active' => true,
                ]);
                $counter++;
            }
        }
    }
} 