<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PatientReferral extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'admission_id',
        'from_ward_id',
        'from_consultant_id',
        'to_specialty_id',
        'to_consultant_id',
        'referral_date',
        'reason',
        'notes',
        'status', // pending, accepted, declined, completed
        'referred_by',
        'response_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'referral_date' => 'datetime',
    ];

    /**
     * Get the patient that was referred
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the admission record
     */
    public function admission()
    {
        return $this->belongsTo(PatientAdmission::class, 'admission_id');
    }

    /**
     * Get the from ward
     */
    public function fromWard()
    {
        return $this->belongsTo(Ward::class, 'from_ward_id');
    }

    /**
     * Get the from consultant
     */
    public function fromConsultant()
    {
        return $this->belongsTo(Consultant::class, 'from_consultant_id');
    }

    /**
     * Get the to specialty
     */
    public function toSpecialty()
    {
        return $this->belongsTo(Specialty::class, 'to_specialty_id');
    }

    /**
     * Get the to consultant
     */
    public function toConsultant()
    {
        return $this->belongsTo(Consultant::class, 'to_consultant_id');
    }

    /**
     * Get the user who referred the patient
     */
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Get formatted referral date
     */
    public function getFormattedReferralDateAttribute()
    {
        return $this->referral_date ? $this->referral_date->format('d M Y, H:i') : null;
    }
} 