<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'bed_id',
        'ward_id',
        'item_name',
        'meal_type',
        'dietary_restriction',
        'status',
        'order_time',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'order_time' => 'datetime',
    ];

    /**
     * Get the patient that placed the order.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the bed associated with the order.
     */
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * Get the ward associated with the order.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Get the status badge class.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        return [
            'pending' => 'bg-warning',
            'preparing' => 'bg-info',
            'ready' => 'bg-primary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger',
        ][$this->status] ?? 'bg-secondary';
    }
} 