<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_number',
        'status',
        'ward_id',
        'consultant_id',
        'nurse_id',
        'patient_id',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Possible bed statuses
     */
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_CLEANING_NEEDED = 'cleaning_needed';

    /**
     * Get all possible bed statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_MAINTENANCE => 'Under Maintenance',
            self::STATUS_CLEANING_NEEDED => 'Cleaning Needed',
        ];
    }

    /**
     * Get the ward that this bed belongs to
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the consultant assigned to this bed
     */
    public function consultant()
    {
        return $this->belongsTo(Consultant::class);
    }

    /**
     * Get the nurse assigned to this bed
     */
    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    /**
     * Get the patient assigned to this bed
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute()
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_AVAILABLE:
                return 'badge-success';
            case self::STATUS_OCCUPIED:
                return 'badge-danger';
            case self::STATUS_MAINTENANCE:
                return 'badge-secondary';
            case self::STATUS_CLEANING_NEEDED:
                return 'badge-warning';
            default:
                return 'badge-info';
        }
    }
} 