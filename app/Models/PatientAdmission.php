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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'admission_date' => 'datetime',
        'is_active' => 'boolean',
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
}
