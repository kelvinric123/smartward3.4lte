<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PatientAdmission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'ward_id',
        'bed_id',
        'bed_number',
        'admission_date',
        'consultant_id',
        'nurse_id',
        'admitted_by',
        'admission_notes',
        'is_active',
        'risk_factors',
        'diet_type',
        'patient_class',
        'expected_discharge_date',
        'expected_length_of_stay',
        'fall_risk_alert',
        'isolation_precautions',
        'clinical_alerts',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'admission_date' => 'datetime',
        'expected_discharge_date' => 'datetime',
        'is_active' => 'boolean',
        'risk_factors' => 'json',
        'expected_length_of_stay' => 'integer',
    ];

    /**
     * Get the patient that was admitted
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the ward the patient was admitted to
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the bed the patient was admitted to
     */
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Get the consultant assigned to this admission
     */
    public function consultant()
    {
        return $this->belongsTo(Consultant::class);
    }

    /**
     * Get the nurse assigned to this admission
     */
    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    /**
     * Get the user who admitted the patient
     */
    public function admittedBy()
    {
        return $this->belongsTo(User::class, 'admitted_by');
    }

    /**
     * Get the formatted admission date
     */
    public function getFormattedAdmissionDateAttribute()
    {
        if (!$this->admission_date) {
            return null;
        }
        
        // Ensure the date is displayed in KL timezone
        return Carbon::parse($this->admission_date)
            ->setTimezone('Asia/Kuala_Lumpur')
            ->format('d M Y, h:i A');
    }

    /**
     * Get admission duration (time since admission)
     */
    public function getStayDurationAttribute()
    {
        if (!$this->admission_date) {
            return 'Unknown';
        }
        
        $now = Carbon::now();
        $admissionDate = Carbon::parse($this->admission_date);
        
        $days = $admissionDate->diffInDays($now);
        $hours = $admissionDate->copy()->addDays($days)->diffInHours($now);
        
        return $days . ' days, ' . $hours . ' hours';
    }

    /**
     * Get diet type options for forms and display
     */
    public static function getDietTypeOptions()
    {
        return [
            'REG' => 'Regular',
            'NPO' => 'Nothing by mouth (NPO)',
            'CLF' => 'Clear liquid',
            'FLF' => 'Full liquid',
            'LCH' => 'Low cholesterol',
            'LCS' => 'Low calorie',
            'LNS' => 'Low sodium',
            'DBT' => 'Diabetic',
            'VEG' => 'Vegetarian',
            'VGN' => 'Vegan',
            'HSL' => 'Halal',
            'KSH' => 'Kosher',
            'RST' => 'Renal/Dialysis',
            'CAR' => 'Cardiac',
            'SFT' => 'Soft',
            'BLD' => 'Bland',
            'PUR' => 'Pureed'
        ];
    }

    /**
     * Get patient class options for forms and display
     */
    public static function getPatientClassOptions()
    {
        return [
            'I' => 'Inpatient',
            'O' => 'Outpatient',
            'A' => 'Ambulatory',
            'E' => 'Emergency',
            'N' => 'Not applicable',
            'R' => 'Recurring patient',
            'B' => 'Obstetrics',
            'C' => 'Commercial Account',
            'U' => 'Unknown'
        ];
    }

    /**
     * Get fall risk alert options for forms and display
     */
    public static function getFallRiskAlertOptions()
    {
        return [
            'NO' => 'No risk',
            'LOW' => 'Low risk',
            'MOD' => 'Moderate risk',
            'HIGH' => 'High risk',
            'FR' => 'Fall Risk Alert Active'
        ];
    }

    /**
     * Get isolation precautions options for forms and display
     */
    public static function getIsolationPrecautionsOptions()
    {
        return [
            'NONE' => 'No isolation',
            'STD' => 'Standard precautions',
            'CON' => 'Contact precautions',
            'DROP' => 'Droplet precautions',
            'AIR' => 'Airborne precautions',
            'DAC' => 'Droplet, Airborne, Contact',
            'DC' => 'Droplet and Contact',
            'AC' => 'Airborne and Contact',
            'AD' => 'Airborne and Droplet'
        ];
    }

    /**
     * Get formatted diet type for display
     */
    public function getFormattedDietTypeAttribute()
    {
        $options = self::getDietTypeOptions();
        return $options[$this->diet_type] ?? $this->diet_type;
    }

    /**
     * Get formatted patient class for display
     */
    public function getFormattedPatientClassAttribute()
    {
        $options = self::getPatientClassOptions();
        return $options[$this->patient_class] ?? $this->patient_class;
    }

    /**
     * Get formatted fall risk alert for display
     */
    public function getFormattedFallRiskAlertAttribute()
    {
        $options = self::getFallRiskAlertOptions();
        return $options[$this->fall_risk_alert] ?? $this->fall_risk_alert;
    }

    /**
     * Get formatted isolation precautions for display
     */
    public function getFormattedIsolationPrecautionsAttribute()
    {
        $options = self::getIsolationPrecautionsOptions();
        return $options[$this->isolation_precautions] ?? $this->isolation_precautions;
    }

    /**
     * Get formatted expected discharge date
     */
    public function getFormattedExpectedDischargeDateAttribute()
    {
        if (!$this->expected_discharge_date) {
            return null;
        }
        
        return Carbon::parse($this->expected_discharge_date)
            ->setTimezone('Asia/Kuala_Lumpur')
            ->format('d M Y, h:i A');
    }

    /**
     * Get badge class for fall risk alert
     */
    public function getFallRiskBadgeClassAttribute()
    {
        return [
            'NO' => 'badge-success',
            'LOW' => 'badge-info',
            'MOD' => 'badge-warning',
            'HIGH' => 'badge-danger',
            'FR' => 'badge-danger'
        ][$this->fall_risk_alert] ?? 'badge-secondary';
    }

    /**
     * Get badge class for isolation precautions
     */
    public function getIsolationBadgeClassAttribute()
    {
        return [
            'NONE' => 'badge-success',
            'STD' => 'badge-info',
            'CON' => 'badge-warning',
            'DROP' => 'badge-warning',
            'AIR' => 'badge-danger',
            'DAC' => 'badge-danger',
            'DC' => 'badge-warning',
            'AC' => 'badge-danger',
            'AD' => 'badge-danger'
        ][$this->isolation_precautions] ?? 'badge-secondary';
    }
}
