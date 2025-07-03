<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientSatisfactionSurvey extends Model
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
        'care_rating',
        'staff_rating',
        'clean_rating',
        'comm_rating',
        'comments',
        'overall_rating',
        'response_type',
        'category'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'overall_rating' => 'decimal:2'
    ];
    
    /**
     * Get the patient that this survey belongs to.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    /**
     * Get the ward where this survey was taken.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }
    
    /**
     * Calculate and set the overall rating based on individual ratings.
     */
    public function calculateOverallRating()
    {
        $ratings = collect([
            $this->care_rating,
            $this->staff_rating,
            $this->clean_rating,
            $this->comm_rating
        ])->filter(function ($rating) {
            return !is_null($rating) && $rating > 0;
        });
        
        if ($ratings->isEmpty()) {
            return null;
        }
        
        $average = $ratings->average();
        $this->overall_rating = round($average, 2);
        
        // Set response type based on overall rating
        if ($average >= 4) {
            $this->response_type = 'positive';
        } elseif ($average >= 3) {
            $this->response_type = 'neutral';
        } else {
            $this->response_type = 'negative';
        }
        
        return $this->overall_rating;
    }
    
    /**
     * Automatically calculate overall rating when saving.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($survey) {
            $survey->calculateOverallRating();
        });
    }
}
