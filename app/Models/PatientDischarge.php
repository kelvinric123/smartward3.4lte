<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PatientDischarge extends Model
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
        'bed_number',
        'discharge_date',
        'discharge_type',
        'discharged_by',
        'discharge_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'discharge_date' => 'datetime',
    ];

    /**
     * Get the patient that was discharged
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the ward the patient was discharged from
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the user who discharged the patient
     */
    public function dischargedBy()
    {
        return $this->belongsTo(User::class, 'discharged_by');
    }

    /**
     * Get the formatted discharge date
     */
    public function getFormattedDischargeDateAttribute()
    {
        if (!$this->discharge_date) {
            return null;
        }
        
        // Ensure the date is displayed in KL timezone
        return Carbon::parse($this->discharge_date)
            ->setTimezone('Asia/Kuala_Lumpur')
            ->format('d M Y, h:i A');
    }
}
