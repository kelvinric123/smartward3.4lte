<?php

namespace Database\Seeders;

use App\Models\Hospital;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NurseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Qmed hospital 1
        $hospital = Hospital::where('name', 'Qmed hospital 1')->first();
        
        if (!$hospital) {
            $this->command->info('Qmed hospital 1 not found. Skipping nurse creation.');
            return;
        }
        
        // Get nurse role
        $nurseRole = Role::where('slug', 'nurse')->first();
        
        if (!$nurseRole) {
            $this->command->info('Nurse role not found. Please run the RoleSeeder first.');
            return;
        }
        
        // Create sample nurses for Qmed hospital 1
        $nurses = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@qmed.hospital',
                'phone' => '+601234567801',
                'password' => '12345678',
            ],
            [
                'name' => 'Michael Lee',
                'email' => 'michael.lee@qmed.hospital',
                'phone' => '+601234567802',
                'password' => '12345678',
            ],
            [
                'name' => 'Emily Wong',
                'email' => 'emily.wong@qmed.hospital',
                'phone' => '+601234567803',
                'password' => '12345678',
            ],
            [
                'name' => 'David Chen',
                'email' => 'david.chen@qmed.hospital',
                'phone' => '+601234567804',
                'password' => '12345678',
            ],
            [
                'name' => 'Lisa Rahman',
                'email' => 'lisa.rahman@qmed.hospital',
                'phone' => '+601234567805',
                'password' => '12345678',
            ],
            [
                'name' => 'James Tan',
                'email' => 'james.tan@qmed.hospital',
                'phone' => '+601234567806',
                'password' => '12345678',
            ],
            [
                'name' => 'Jennifer Lim',
                'email' => 'jennifer.lim@qmed.hospital',
                'phone' => '+601234567807',
                'password' => '12345678',
            ],
            [
                'name' => 'Robert Kumar',
                'email' => 'robert.kumar@qmed.hospital',
                'phone' => '+601234567808',
                'password' => '12345678',
            ],
            [
                'name' => 'Jessica Chong',
                'email' => 'jessica.chong@qmed.hospital',
                'phone' => '+601234567809',
                'password' => '12345678',
            ],
            [
                'name' => 'Daniel Ong',
                'email' => 'daniel.ong@qmed.hospital',
                'phone' => '+601234567810',
                'password' => '12345678',
            ],
        ];
        
        foreach ($nurses as $nurseData) {
            // Check if the nurse already exists
            $existingNurse = User::where('email', $nurseData['email'])->first();
            
            if (!$existingNurse) {
                // Create the nurse
                $nurse = User::create([
                    'name' => $nurseData['name'],
                    'email' => $nurseData['email'],
                    'phone' => $nurseData['phone'],
                    'password' => Hash::make($nurseData['password']),
                    'active' => true,
                    'hospital_id' => $hospital->id,
                    'email_verified_at' => now(),
                ]);
                
                // Assign nurse role
                $nurse->roles()->attach($nurseRole);
                
                $this->command->info("Created nurse: {$nurse->name}");
            } else {
                $this->command->info("Nurse with email {$nurseData['email']} already exists. Skipping.");
            }
        }
    }
} 