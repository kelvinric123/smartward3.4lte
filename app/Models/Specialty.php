<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'hospital_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function consultants()
    {
        return $this->hasMany(Consultant::class);
    }
    
    /**
     * Get the wards associated with this specialty (many-to-many relationship).
     */
    public function wards()
    {
        return $this->belongsToMany(Ward::class, 'ward_specialty');
    }
} 