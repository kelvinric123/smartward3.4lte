<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'mrn', // Medical Record Number
        'identity_number', // IC or Passport
        'identity_type', // 'ic' or 'passport'
        'age',
        'gender',
        'email',
        'phone',
        'address',
    ];

    /**
     * Calculate age from Malaysian IC number
     * Format: YYMMDD-PB-###G
     * 
     * @param string $ic
     * @return int|null
     */
    public static function calculateAgeFromIC($ic)
    {
        if (empty($ic) || strlen($ic) < 6) {
            return null;
        }

        // Extract birth date from IC (first 6 digits: YYMMDD)
        $birthDateStr = substr($ic, 0, 6);
        
        // Extract year, month, day
        $year = substr($birthDateStr, 0, 2);
        $month = substr($birthDateStr, 2, 2);
        $day = substr($birthDateStr, 4, 2);
        
        // Determine century: If year is greater than current year's last 2 digits, 
        // then it's from the previous century
        $currentYear = date('y');
        if ($year > $currentYear) {
            $fullYear = '19' . $year;
        } else {
            $fullYear = '20' . $year;
        }
        
        try {
            $birthDate = Carbon::createFromFormat('Y-m-d', "$fullYear-$month-$day");
            return $birthDate->age;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Determine gender from Malaysian IC number
     * The last digit of IC number: odd = male, even = female
     * 
     * @param string $ic
     * @return string|null
     */
    public static function determineGenderFromIC($ic)
    {
        if (empty($ic)) {
            return null;
        }

        // Get last digit from the IC
        $lastDigit = substr($ic, -1);
        
        if (is_numeric($lastDigit)) {
            return (intval($lastDigit) % 2 == 0) ? 'female' : 'male';
        }
        
        return null;
    }

    /**
     * Get the vital signs recorded for this patient
     */
    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    /**
     * Get the latest vital signs for this patient
     */
    public function latestVitalSigns()
    {
        return $this->hasOne(VitalSign::class)->latest('recorded_at');
    }
} 