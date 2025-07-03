<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PatientSatisfactionSurvey;
use App\Models\Patient;
use App\Models\Ward;
use Carbon\Carbon;

class PatientSatisfactionSurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some patients and wards to use for the surveys
        $patients = Patient::inRandomOrder()->limit(20)->get();
        $wards = Ward::all();
        
        if ($patients->isEmpty() || $wards->isEmpty()) {
            $this->command->info('No patients or wards found. Please seed patients and wards first.');
            return;
        }
        
        // Sample comments for different rating levels
        $positiveComments = [
            'Excellent care from all staff members. Very satisfied with my stay.',
            'Nurses were very attentive and kind. Doctors explained everything clearly.',
            'Clean facilities and great communication from the medical team.',
            'Outstanding service. I felt well cared for throughout my stay.',
            'Professional staff and comfortable environment.',
            'Very impressed with the quality of care and attention to detail.',
        ];
        
        $neutralComments = [
            'Overall okay experience. Some areas could be improved.',
            'Average service. Staff were generally helpful.',
            'Decent care but room could have been cleaner.',
            'Food was okay, staff were professional.',
            'Acceptable stay with minor issues.',
            'Standard care, no major complaints.',
        ];
        
        $negativeComments = [
            'Response time was too slow when I needed assistance.',
            'Room was not as clean as expected. Food quality needs improvement.',
            'Communication could be better. Had to wait too long for updates.',
            'Staff seemed rushed and not very attentive.',
            'Noisy environment made it difficult to rest.',
            'Several issues with the room temperature and lighting.',
        ];
        
        // Create surveys for the last 45 days
        for ($i = 0; $i < 50; $i++) {
            $patient = $patients->random();
            $ward = $wards->random();
            
            // Generate random ratings (1-5 stars)
            $careRating = rand(2, 5);
            $staffRating = rand(2, 5);
            $cleanRating = rand(2, 5);
            $commRating = rand(2, 5);
            
            // Determine sentiment based on average rating
            $avgRating = ($careRating + $staffRating + $cleanRating + $commRating) / 4;
            
            $responseType = 'neutral';
            $comment = collect($neutralComments)->random();
            
            if ($avgRating >= 4) {
                $responseType = 'positive';
                $comment = collect($positiveComments)->random();
            } elseif ($avgRating < 3) {
                $responseType = 'negative';
                $comment = collect($negativeComments)->random();
                
                // Lower ratings for negative feedback
                $careRating = rand(1, 3);
                $staffRating = rand(1, 3);
                $cleanRating = rand(1, 3);
                $commRating = rand(1, 3);
            }
            
            // Sometimes leave some ratings empty (realistic scenario)
            if (rand(1, 10) === 1) $careRating = null;
            if (rand(1, 10) === 1) $staffRating = null;
            if (rand(1, 10) === 1) $cleanRating = null;
            if (rand(1, 10) === 1) $commRating = null;
            
            // Sometimes leave comments empty
            if (rand(1, 5) === 1) $comment = null;
            
            PatientSatisfactionSurvey::create([
                'patient_id' => $patient->id,
                'ward_id' => $ward->id,
                'care_rating' => $careRating,
                'staff_rating' => $staffRating,
                'clean_rating' => $cleanRating,
                'comm_rating' => $commRating,
                'comments' => $comment,
                'category' => 'general',
                'created_at' => Carbon::now()->subDays(rand(0, 45)),
                'updated_at' => Carbon::now()->subDays(rand(0, 45)),
            ]);
        }
        
        $this->command->info('Created 50 patient satisfaction survey records.');
    }
}
