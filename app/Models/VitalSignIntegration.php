<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSignIntegration extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     */
    protected $table = 'vital_sign_integration';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'device_timestamp',
        'raw_message',
        'mrn',
        'patient_name',
        'respiratory_rate',
        'spo2',
        'pulse_rate',
        'systolic_bp',
        'diastolic_bp',
        'avpu',
        'ews_score_total',
        'nurse_id',
        'processed',
        'processed_at',
        'processing_notes',
        'patient_id',
    ];
    
    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'device_timestamp' => 'datetime',
        'processed_at' => 'datetime',
        'processed' => 'boolean',
        'respiratory_rate' => 'decimal:2',
        'spo2' => 'decimal:2',
        'pulse_rate' => 'decimal:2',
        'systolic_bp' => 'decimal:2',
        'diastolic_bp' => 'decimal:2',
        'ews_score_total' => 'integer',
    ];
    
    /**
     * Get the patient associated with this vital sign integration.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    
    /**
     * Scope to get unprocessed records.
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }
    
    /**
     * Scope to get processed records.
     */
    public function scopeProcessed($query)
    {
        return $query->where('processed', true);
    }
    
    /**
     * Mark this record as processed.
     */
    public function markAsProcessed(string $notes = null): bool
    {
        return $this->update([
            'processed' => true,
            'processed_at' => now(),
            'processing_notes' => $notes,
        ]);
    }
    
    /**
     * Get vital signs as an array for easy processing.
     */
    public function getVitalSignsArray(): array
    {
        return [
            'respiratory_rate' => $this->respiratory_rate,
            'spo2' => $this->spo2,
            'pulse_rate' => $this->pulse_rate,
            'systolic_bp' => $this->systolic_bp,
            'diastolic_bp' => $this->diastolic_bp,
            'avpu' => $this->avpu,
            'ews_score_total' => $this->ews_score_total,
        ];
    }
}
