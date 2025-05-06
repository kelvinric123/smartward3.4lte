<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WardAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create ward admin user
        $user = User::create([
            'name' => 'Ward Admin',
            'email' => 'qmedward@qmed.asia',
            'password' => Hash::make('88888888'),
            'active' => true,
        ]);

        // Assign ward admin role
        $role = Role::where('slug', 'ward-admin')->first();
        
        if ($role) {
            $user->roles()->attach($role->id);
        }
    }
} 