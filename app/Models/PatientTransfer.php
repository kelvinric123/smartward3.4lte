<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PatientTransfer extends Model
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
        'to_ward_id',
        'to_bed_id',
        'transfer_date',
        'reason',
        'notes',
        'transferred_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'transfer_date' => 'datetime',
    ];

    /**
     * Get the patient that was transferred
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
     * Get the to ward
     */
    public function toWard()
    {
        return $this->belongsTo(Ward::class, 'to_ward_id');
    }

    /**
     * Get the to bed
     */
    public function toBed()
    {
        return $this->belongsTo(Bed::class, 'to_bed_id');
    }

    /**
     * Get the user who transferred the patient
     */
    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    /**
     * Get formatted transfer date
     */
    public function getFormattedTransferDateAttribute()
    {
        return $this->transfer_date ? $this->transfer_date->format('d M Y, H:i') : null;
    }
}
