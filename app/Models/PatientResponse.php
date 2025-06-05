<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientResponse extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_alert_id',
        'patient_id',
        'nurse_id',
        'response_message',
        'status',
        'read_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];
    
    /**
     * Get the patient alert that this response is for.
     */
    public function patientAlert()
    {
        return $this->belongsTo(PatientAlert::class);
    }
    
    /**
     * Get the patient that this response is for.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    /**
     * Get the nurse/user who responded.
     */
    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
    
    /**
     * Mark the response as read.
     */
    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }
}
