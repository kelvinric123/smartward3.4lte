<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ms_MY'); // Malaysian locale
        
        // Fixed list of patient names
        $patientNames = [
            'Ahmad bin Abdullah',
            'Siti Aishah binti Hassan',
            'Muhammad bin Ibrahim',
            'Nurul Ain binti Yusof',
            'Tan Wei Ming',
            'Wong Li Hua',
            'Lee Chong Wei',
            'Lim Mei Ling',
            'Raj Kumar a/l Muthu',
            'Priya d/o Krishnan',
            'Suresh a/l Gopal',
            'Kavita d/o Anand',
            'Abdul Rahman bin Ismail',
            'Fatimah binti Omar',
            'Chong Wei Feng',
            'Ng Hui Ling',
            'Ravi a/l Pillai',
            'Meena d/o Rajan',
            'Aziz bin Hassan',
            'Zainab binti Abdul Rahman'
        ];
        
        // Helper function to generate a valid Malaysian IC number
        $generateIC = function() use ($faker) {
            // Year (00-99)
            $year = str_pad($faker->numberBetween(60, 99), 2, '0', STR_PAD_LEFT);
            
            // Month (01-12)
            $month = str_pad($faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
            
            // Day (01-31) - simplified, doesn't check for valid dates per month
            $day = str_pad($faker->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);
            
            // Birth place code (01-59)
            $birthplace = str_pad($faker->numberBetween(1, 59), 2, '0', STR_PAD_LEFT);
            
            // Random 3 digit number
            $random = str_pad($faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);
            
            // Last digit - determines gender (odd = male, even = female)
            $genderDigit = $faker->numberBetween(0, 9);
            
            return $year . $month . $day . '-' . $birthplace . '-' . $random . $genderDigit;
        };
        
        // Helper function to generate passport number
        $generatePassport = function() use ($faker) {
            $letters = $faker->randomLetter . $faker->randomLetter;
            $numbers = rand(100000, 999999);
            return strtoupper($letters) . $numbers;
        };
        
        // Generate MRN with a simple numeric format starting from 10000
        $generateMRN = function() use ($faker) {
            return 10000 + $faker->unique()->numberBetween(1, 9999);
        };
        
        // Create patients with fixed names
        foreach ($patientNames as $index => $name) {
            // Randomly decide if this patient uses IC or passport
            $identityType = $faker->randomElement(['ic', 'passport', 'ic', 'ic']); // More weight to IC
            
            // Generate appropriate ID number based on type
            if ($identityType === 'ic') {
                $identityNumber = $generateIC();
                $age = Patient::calculateAgeFromIC($identityNumber);
                $gender = Patient::determineGenderFromIC($identityNumber);
            } else {
                $identityNumber = $generatePassport();
                $age = $faker->numberBetween(18, 80);
                $gender = $faker->randomElement(['male', 'female']);
            }
            
            Patient::create([
                'name' => $name,
                'mrn' => 10000 + $index,
                'identity_number' => $identityNumber,
                'identity_type' => $identityType,
                'age' => $age,
                'gender' => $gender,
                'email' => $faker->optional(0.7)->safeEmail, // 70% chance to have email
                'phone' => $faker->optional(0.9)->phoneNumber, // 90% chance to have phone
                'address' => $faker->optional(0.8)->address, // 80% chance to have address
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => now()
            ]);
        }
    }
}
