<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Super Administrator with full system access',
            ],
            [
                'name' => 'Hospital Admin',
                'slug' => 'hospital-admin',
                'description' => 'Hospital Administrator with access to hospital management',
            ],
            [
                'name' => 'Consultant',
                'slug' => 'consultant',
                'description' => 'Medical Consultant with specialized access',
            ],
            [
                'name' => 'GP Doctor',
                'slug' => 'gp-doctor',
                'description' => 'General Practitioner Doctor',
            ],
            [
                'name' => 'Booking Agent',
                'slug' => 'booking-agent',
                'description' => 'Agent responsible for managing bookings',
            ],
            [
                'name' => 'Nurse',
                'slug' => 'nurse',
                'description' => 'Nurse with patient care access',
            ],
            [
                'name' => 'Ward Admin',
                'slug' => 'ward-admin',
                'description' => 'Ward Administrator with dashboard view access',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
