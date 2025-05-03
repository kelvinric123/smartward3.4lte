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
        
        // Generate Malaysian names
        $malaysianFirstNames = [
            'Ahmad', 'Muhammad', 'Ali', 'Ibrahim', 'Ismail', 'Abdul', 'Hassan', 'Yusof', 'Omar', 'Aziz',
            'Fatimah', 'Siti', 'Aishah', 'Aminah', 'Zainab', 'Nor', 'Farah', 'Sarah', 'Nurul', 'Nadia',
            'Chong', 'Tan', 'Wong', 'Lim', 'Lee', 'Ng', 'Ooi', 'Cheah', 'Goh', 'Teoh',
            'Raj', 'Kumar', 'Ravi', 'Siva', 'Muthu', 'Suresh', 'Ramesh', 'Ganesh', 'Vijay', 'Arvind',
            'Lakshmi', 'Priya', 'Devi', 'Anita', 'Kavita', 'Meena', 'Rani', 'Uma', 'Sunita', 'Shanti'
        ];
        
        $malaysianLastNames = [
            'bin Abdullah', 'bin Mohamed', 'bin Rahman', 'bin Ismail', 'bin Ibrahim', 'bin Hassan', 
            'binti Omar', 'binti Yusof', 'binti Abdul Rahman', 'binti Aziz', 'binti Hassan',
            'Wei', 'Ming', 'Hong', 'Ling', 'Hui', 'Xin', 'Jie', 'Feng', 'Cheng', 'Yong',
            'Kumar', 'Singh', 'Kaur', 'Raj', 'Rajan', 'Pillai', 'Muthu', 'Krishnan', 'Gopal', 'Anand'
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
        
        // Generate MRN with the format MRN-YYYYMMDD-XXXX
        $generateMRN = function() use ($faker) {
            $date = $faker->dateTimeBetween('-3 years', 'now')->format('Ymd');
            $number = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            return 'MRN-' . $date . '-' . $number;
        };
        
        // Generate 50 patients
        for ($i = 0; $i < 50; $i++) {
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
            
            // Generate full name based on common Malaysian patterns
            $firstName = $faker->randomElement($malaysianFirstNames);
            $lastName = $faker->randomElement($malaysianLastNames);
            $fullName = $firstName . ' ' . $lastName;
            
            Patient::create([
                'name' => $fullName,
                'mrn' => $generateMRN(),
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
