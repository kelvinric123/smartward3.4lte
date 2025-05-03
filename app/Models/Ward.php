<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'hospital_id',
        'specialty_id',
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
     * Get the specialty associated with the ward.
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }
    
    /**
     * Get the beds in this ward.
     */
    public function beds()
    {
        return $this->hasMany(Bed::class);
    }
} 