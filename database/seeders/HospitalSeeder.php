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
                'name' => 'Hospital Kuala Lumpur',
                'address' => 'Jalan Pahang',
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan',
                'country' => 'Malaysia',
                'postal_code' => '50586',
                'phone' => '03-2615 5555',
                'email' => 'info@hkl.gov.my',
                'website' => 'www.hkl.gov.my',
            ],
            [
                'name' => 'Hospital Pulau Pinang',
                'address' => 'Jalan Residensi',
                'city' => 'Georgetown',
                'state' => 'Pulau Pinang',
                'country' => 'Malaysia',
                'postal_code' => '10990',
                'phone' => '+60 4-222 5333',
                'email' => 'info@hpp.moh.gov.my',
                'website' => 'https://hpp.moh.gov.my',
                'is_active' => true,
            ],
            [
                'name' => 'Gleneagles Hospital Kuala Lumpur',
                'address' => '286, Jalan Ampang',
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan',
                'country' => 'Malaysia',
                'postal_code' => '50450',
                'phone' => '03-4141 3000',
                'email' => 'info@gleneagles.com.my',
                'website' => 'www.gleneagles.com.my',
            ],
            [
                'name' => 'Pantai Hospital Kuala Lumpur',
                'address' => '8, Jalan Bukit Pantai',
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan',
                'country' => 'Malaysia',
                'postal_code' => '59100',
                'phone' => '03-2296 0888',
                'email' => 'info@pantai.com.my',
                'website' => 'www.pantai.com.my',
            ],
            [
                'name' => 'Sunway Medical Centre',
                'address' => '5, Jalan Lagoon Selatan, Bandar Sunway',
                'city' => 'Petaling Jaya',
                'state' => 'Selangor',
                'country' => 'Malaysia',
                'postal_code' => '47500',
                'phone' => '03-7491 9191',
                'email' => 'info@sunway.com.my',
                'website' => 'www.sunway.com.my',
            ],
            [
                'name' => 'Hospital Sultanah Aminah',
                'address' => 'Jalan Persiaran Abu Bakar Sultan',
                'city' => 'Johor Bahru',
                'state' => 'Johor',
                'country' => 'Malaysia',
                'postal_code' => '80100',
                'phone' => '+60 7-223 1666',
                'email' => 'info@hsa.moh.gov.my',
                'website' => 'https://hsa.moh.gov.my',
                'is_active' => true,
            ],
            [
                'name' => 'Prince Court Medical Centre',
                'address' => '39, Jalan Kia Peng',
                'city' => 'Kuala Lumpur',
                'state' => 'Wilayah Persekutuan',
                'country' => 'Malaysia',
                'postal_code' => '50450',
                'phone' => '03-2160 0000',
                'email' => 'info@princecourt.com',
                'website' => 'www.princecourt.com',
            ],
        ];

        foreach ($hospitals as $hospital) {
            Hospital::create($hospital + ['is_active' => true]);
        }
    }
}
