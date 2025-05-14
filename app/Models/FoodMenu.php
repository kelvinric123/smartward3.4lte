<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodMenu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'meal_type',
        'dietary_tags',
        'available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'available' => 'boolean',
    ];

    /**
     * Get food orders associated with this menu item.
     */
    public function foodOrders()
    {
        return $this->hasMany(FoodOrder::class, 'item_name', 'name');
    }
} 