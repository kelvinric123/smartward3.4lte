<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'type',
        'title',
        'description',
        'date_diagnosed',
        'severity',
        'status',
        'notes'
    ];

    protected $casts = [
        'date_diagnosed' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
