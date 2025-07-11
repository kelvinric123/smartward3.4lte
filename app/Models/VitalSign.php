<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VitalSign extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'recorded_by',
        'recorded_at',
        'temperature',
        'heart_rate',
        'respiratory_rate',
        'systolic_bp',
        'diastolic_bp',
        'oxygen_saturation',
        'consciousness',
        'gcs_total',
        'gcs_eye',
        'gcs_verbal',
        'gcs_motor',
        'gcs_score',
        'temperature_score',
        'heart_rate_score',
        'respiratory_rate_score',
        'blood_pressure_score',
        'oxygen_saturation_score',
        'consciousness_score',
        'total_ews',
        'notes',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature' => 'float',
        'heart_rate' => 'float',
        'respiratory_rate' => 'float',
        'systolic_bp' => 'float',
        'diastolic_bp' => 'float',
        'oxygen_saturation' => 'float',
        'gcs_total' => 'integer',
        'gcs_eye' => 'integer',
        'gcs_verbal' => 'integer',
        'gcs_motor' => 'integer',
        'gcs_score' => 'integer',
        'temperature_score' => 'integer',
        'heart_rate_score' => 'integer',
        'respiratory_rate_score' => 'integer',
        'blood_pressure_score' => 'integer',
        'oxygen_saturation_score' => 'integer',
        'consciousness_score' => 'integer',
        'total_ews' => 'integer',
    ];

    /**
     * Get the patient that these vital signs belong to
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the user who recorded these vital signs
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Calculate the Early Warning Score based on vital signs
     */
    public static function calculateEWS($vitals)
    {
        $scores = [
            'temperature_score' => self::calculateTemperatureScore($vitals['temperature'] ?? null),
            'heart_rate_score' => self::calculateHeartRateScore($vitals['heart_rate'] ?? null),
            'respiratory_rate_score' => self::calculateRespiratoryRateScore($vitals['respiratory_rate'] ?? null),
            'blood_pressure_score' => self::calculateBloodPressureScore($vitals['systolic_bp'] ?? null),
            'oxygen_saturation_score' => self::calculateOxygenSaturationScore($vitals['oxygen_saturation'] ?? null),
            'consciousness_score' => self::calculateConsciousnessScore($vitals['consciousness'] ?? null),
        ];

        $totalScore = array_sum($scores);
        
        return [
            'scores' => $scores,
            'total_ews' => $totalScore
        ];
    }

    /**
     * Calculate Temperature Score
     */
    private static function calculateTemperatureScore($temperature)
    {
        if ($temperature === null) return 0;
        
        if ($temperature <= 35.0) return 3;
        if ($temperature <= 36.0) return 1;
        if ($temperature >= 36.1 && $temperature <= 38.0) return 0;
        if ($temperature >= 38.1 && $temperature <= 39.0) return 1;
        if ($temperature >= 39.1) return 2;
        
        return 0;
    }

    /**
     * Calculate Heart Rate Score
     */
    private static function calculateHeartRateScore($heartRate)
    {
        if ($heartRate === null) return 0;
        
        if ($heartRate <= 40) return 3;
        if ($heartRate >= 41 && $heartRate <= 50) return 1;
        if ($heartRate >= 51 && $heartRate <= 90) return 0;
        if ($heartRate >= 91 && $heartRate <= 110) return 1;
        if ($heartRate >= 111 && $heartRate <= 130) return 2;
        if ($heartRate >= 131) return 3;
        
        return 0;
    }

    /**
     * Calculate Respiratory Rate Score
     */
    private static function calculateRespiratoryRateScore($respRate)
    {
        if ($respRate === null) return 0;
        
        if ($respRate <= 8) return 3;
        if ($respRate >= 9 && $respRate <= 11) return 1;
        if ($respRate >= 12 && $respRate <= 20) return 0;
        if ($respRate >= 21 && $respRate <= 24) return 2;
        if ($respRate >= 25) return 3;
        
        return 0;
    }

    /**
     * Calculate Blood Pressure Score (based on systolic BP)
     */
    private static function calculateBloodPressureScore($systolicBP)
    {
        if ($systolicBP === null) return 0;
        
        if ($systolicBP <= 90) return 3;
        if ($systolicBP >= 91 && $systolicBP <= 100) return 2;
        if ($systolicBP >= 101 && $systolicBP <= 110) return 1;
        if ($systolicBP >= 111 && $systolicBP <= 219) return 0;
        if ($systolicBP >= 220) return 3;
        
        return 0;
    }

    /**
     * Calculate Oxygen Saturation Score
     */
    private static function calculateOxygenSaturationScore($o2Sat)
    {
        if ($o2Sat === null) return 0;
        
        if ($o2Sat <= 91) return 3;
        if ($o2Sat >= 92 && $o2Sat <= 93) return 2;
        if ($o2Sat >= 94 && $o2Sat <= 95) return 1;
        if ($o2Sat >= 96) return 0;
        
        return 0;
    }

    /**
     * Calculate Consciousness Score
     */
    private static function calculateConsciousnessScore($consciousness)
    {
        if ($consciousness === null || $consciousness === 'A' || $consciousness === 'Alert') return 0;
        
        // Any value other than Alert (V, P, U) gets 3 points
        return 3;
    }

    /**
     * Get the formatted recorded at time
     */
    public function getFormattedRecordedAtAttribute()
    {
        return $this->recorded_at ? $this->recorded_at->format('d M Y, h:ia') : null;
    }

    /**
     * Get the GCS formatted display
     */
    public function getGcsDisplayAttribute()
    {
        if ($this->gcs_total) {
            return "GCS {$this->gcs_total} (E{$this->gcs_eye}V{$this->gcs_verbal}M{$this->gcs_motor})";
        }
        return null;
    }

    /**
     * Get GCS status color based on score
     */
    public function getGcsStatusColorAttribute()
    {
        if (!$this->gcs_total) return 'secondary';
        
        if ($this->gcs_total <= 8) return 'danger';     // Severe brain injury
        if ($this->gcs_total <= 12) return 'warning';   // Moderate brain injury
        if ($this->gcs_total <= 14) return 'info';      // Mild brain injury
        return 'success';                                 // Normal
    }

    /**
     * Get the clinical status based on EWS score
     */
    public function getClinicalStatusAttribute()
    {
        if ($this->total_ews >= 7) return 'Critical';
        if ($this->total_ews >= 5) return 'High Risk';
        if ($this->total_ews >= 3) return 'Medium Risk';
        return 'Low Risk';
    }

    /**
     * Get the status color based on EWS score
     */
    public function getStatusColorAttribute()
    {
        if ($this->total_ews >= 7) return 'danger';
        if ($this->total_ews >= 5) return 'warning';
        if ($this->total_ews >= 3) return 'info';
        return 'success';
    }
} 