<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PatientMovement extends Model
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
        'from_bed_id',
        'to_service_location',
        'scheduled_time',
        'sent_time',
        'return_time',
        'status', // scheduled, sent, returned, cancelled
        'notes',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'scheduled_time' => 'datetime',
        'sent_time' => 'datetime', 
        'return_time' => 'datetime',
    ];

    /**
     * Get the patient
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
     * Get the from bed
     */
    public function fromBed()
    {
        return $this->belongsTo(Bed::class, 'from_bed_id');
    }

    /**
     * Get the user who created the movement
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted scheduled time
     */
    public function getFormattedScheduledTimeAttribute()
    {
        return $this->scheduled_time ? $this->scheduled_time->format('d M Y, H:i') : null;
    }

    /**
     * Get formatted sent time
     */
    public function getFormattedSentTimeAttribute()
    {
        return $this->sent_time ? $this->sent_time->format('d M Y, H:i') : null;
    }
} 