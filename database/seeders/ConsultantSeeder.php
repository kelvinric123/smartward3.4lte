<?php

namespace Database\Seeders;

use App\Models\Consultant;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class ConsultantSeeder extends Seeder
{
    public function run(): void
    {
        $consultantsBySpecialty = [
            'Medical' => [
                [
                    'name' => 'Dr. Ahmad bin Abdullah',
                    'email' => 'ahmad.abdullah',
                    'phone' => '0123456789',
                    'qualification' => 'MBBS (UM), MRCP (UK), FRCP (Edin)',
                    'experience_years' => 15,
                    'bio' => 'Senior Consultant in Internal Medicine with extensive experience in general medical care.',
                ],
                [
                    'name' => 'Dr. Siti binti Ismail',
                    'email' => 'siti.ismail',
                    'phone' => '0123456790',
                    'qualification' => 'MBBS (UM), MD (Internal Medicine), FRCP (Lond)',
                    'experience_years' => 12,
                    'bio' => 'Medical specialist with expertise in diabetes and hypertension management.',
                ],
            ],
            'Surgical' => [
                [
                    'name' => 'Dr. Rajesh a/l Kumar',
                    'email' => 'rajesh.kumar',
                    'phone' => '0123456791',
                    'qualification' => 'MBBS (UM), MS (General Surgery), FRCS (Edin)',
                    'experience_years' => 18,
                    'bio' => 'General surgeon specializing in laparoscopic and emergency surgery.',
                ],
                [
                    'name' => 'Dr. Wong Mei Ling',
                    'email' => 'wong.meiling',
                    'phone' => '0123456792',
                    'qualification' => 'MBBS (UM), MS (Surgery), FRCS (Glas)',
                    'experience_years' => 14,
                    'bio' => 'Surgical specialist with expertise in trauma and emergency surgery.',
                ],
            ],
            'Orthopedics' => [
                [
                    'name' => 'Dr. Nor Azizah binti Omar',
                    'email' => 'nor.azizah',
                    'phone' => '0123456793',
                    'qualification' => 'MBBS (UM), MS (Ortho), FRCS (Ortho)',
                    'experience_years' => 16,
                    'bio' => 'Orthopedic surgeon specializing in joint replacement and sports injuries.',
                ],
                [
                    'name' => 'Dr. Michael Chen',
                    'email' => 'michael.chen',
                    'phone' => '0123456794',
                    'qualification' => 'MBBS (UM), MS (Ortho), FRCS (Edin)',
                    'experience_years' => 11,
                    'bio' => 'Orthopedic specialist with focus on spine surgery and trauma.',
                ],
            ],
            'Paediatrics' => [
                [
                    'name' => 'Dr. Sarah Johnson',
                    'email' => 'sarah.johnson',
                    'phone' => '0123456795',
                    'qualification' => 'MBBS (UM), MD (Pediatrics), FRCPCH (UK)',
                    'experience_years' => 13,
                    'bio' => 'Pediatrician with special interest in developmental pediatrics and neonatology.',
                ],
                [
                    'name' => 'Dr. David Tan',
                    'email' => 'david.tan',
                    'phone' => '0123456796',
                    'qualification' => 'MBBS (UM), MD (Pediatrics), MRCPCH (UK)',
                    'experience_years' => 9,
                    'bio' => 'Pediatric specialist with expertise in childhood infectious diseases.',
                ],
            ],
        ];

        $specialties = Specialty::all();
        $counter = 1;

        foreach ($specialties as $specialty) {
            $consultantsForSpecialty = $consultantsBySpecialty[$specialty->name] ?? [];
            
            foreach ($consultantsForSpecialty as $consultant) {
                $hospitalName = str_replace(['(', ')', ' '], '', $specialty->hospital->name);
                Consultant::create([
                    'name' => $consultant['name'],
                    'email' => $consultant['email'] . '.' . $counter . '@' . strtolower($hospitalName) . '.com',
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