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
    
    /**
     * Get the discharge records for this patient
     */
    public function discharges()
    {
        return $this->hasMany(PatientDischarge::class);
    }
    
    /**
     * Get the latest discharge record
     */
    public function latestDischarge()
    {
        return $this->hasOne(PatientDischarge::class)->latest('discharge_date');
    }
    
    /**
     * Get the admission records for this patient
     */
    public function admissions()
    {
        return $this->hasMany(PatientAdmission::class);
    }
    
    /**
     * Get the current active admission
     */
    public function activeAdmission()
    {
        return $this->hasOne(PatientAdmission::class)->where('is_active', true)->latest('admission_date');
    }
    
    /**
     * Get the latest admission record
     */
    public function latestAdmission()
    {
        return $this->hasOne(PatientAdmission::class)->latest('admission_date');
    }
    
    /**
     * Get the bed this patient is currently assigned to
     */
    public function bed()
    {
        return $this->hasOne(Bed::class);
    }
    
    /**
     * Check if the patient is currently admitted
     */
    public function getIsAdmittedAttribute()
    {
        return $this->bed()->exists();
    }
    
    /**
     * Get all referrals for this patient
     */
    public function referrals()
    {
        return $this->hasMany(PatientReferral::class)->orderBy('referral_date', 'desc');
    }
    
    /**
     * Get all bed transfers for this patient
     */
    public function bedTransfers()
    {
        return $this->hasMany(PatientTransfer::class)->orderBy('transfer_date', 'desc');
    }
    
    /**
     * Get the medications for this patient
     */
    public function medications()
    {
        return $this->hasMany(Medication::class);
    }
    
    /**
     * Get active medications for this patient
     */
    public function activeMedications()
    {
        return $this->hasMany(Medication::class)->where('status', 'active');
    }
    
    /**
     * Get the medical history for this patient
     */
    public function medicalHistories()
    {
        return $this->hasMany(MedicalHistory::class);
    }
    
    /**
     * Get the responses/notifications for this patient
     */
    public function responses()
    {
        return $this->hasMany(PatientResponse::class);
    }
    
    /**
     * Get unread responses for this patient
     */
    public function unreadResponses()
    {
        return $this->hasMany(PatientResponse::class)->where('status', 'sent');
    }
} 