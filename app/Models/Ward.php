<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'capacity',
        'hospital_id',
        'specialty_id', // Keep for backward compatibility during migration
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    /**
     * Get the hospital that owns the ward.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the specialty associated with the ward (legacy single relationship).
     * This is kept for backward compatibility during migration.
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }
    
    /**
     * Get the specialties associated with the ward (new many-to-many relationship).
     */
    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'ward_specialty');
    }
    
    /**
     * Get the beds in this ward.
     */
    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
} 