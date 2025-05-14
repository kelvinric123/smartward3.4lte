<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAlert extends Model
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
        'alert_type',
        'message',
        'status',
        'is_urgent',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_urgent' => 'boolean',
    ];
    
    /**
     * Get the patient that owns the alert.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    /**
     * Get the ward that owns the alert.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }
    
    /**
     * Get the bed that owns the alert.
     */
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }
}
