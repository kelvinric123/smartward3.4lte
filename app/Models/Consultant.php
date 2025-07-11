<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'qualification',
        'experience_years',
        'specialty_id',
        'hospital_id',
        'is_active',
        'bio',
        'photo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'experience_years' => 'integer',
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
} 