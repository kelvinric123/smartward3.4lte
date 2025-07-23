<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = [
            [
                'name' => 'Pantai HKL(DEMO)',
                'address' => '8, Jalan Bukit Pantai',
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan',
                'country' => 'Malaysia',
                'postal_code' => '59100',
                'phone' => '03-2296 0888',
                'email' => 'info@pantai-hkl.com.my',
                'website' => 'www.pantai-hkl.com.my',
                'is_active' => true,
            ],
            [
                'name' => 'Glenegle KL(DEMO)',
                'address' => '286, Jalan Ampang',
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan',
                'country' => 'Malaysia',
                'postal_code' => '50450',
                'phone' => '03-4141 3000',
                'email' => 'info@gleneagles-kl.com.my',
                'website' => 'www.gleneagles-kl.com.my',
                'is_active' => true,
            ],
        ];

        foreach ($hospitals as $hospital) {
            Hospital::create($hospital);
        }
    }
}
