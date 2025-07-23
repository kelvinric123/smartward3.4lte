<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NurseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ward_id',
        'schedule_data',
        'original_filename',
        'schedule_start_date',
        'schedule_end_date',
        'is_active'
    ];

    protected $casts = [
        'schedule_data' => 'array',
        'schedule_start_date' => 'date',
        'schedule_end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function getScheduleForDate($date)
    {
        $assignments = collect($this->schedule_data['assignments'] ?? []);
        return $assignments->filter(function ($assignment) use ($date) {
            return $assignment['assignment_date'] === $date;
        })->groupBy('shift_slot.name');
    }

    public function getCurrentNurses()
    {
        $today = now()->format('Y-m-d');
        return $this->getScheduleForDate($today);
    }

    public function getAvailableDates()
    {
        $assignments = collect($this->schedule_data['assignments'] ?? []);
        return $assignments->pluck('assignment_date')->unique()->sort()->values();
    }

    public function getShiftSlots()
    {
        $schedules = collect($this->schedule_data['roster_info']['schedules'] ?? []);
        $shifts = collect();
        
        foreach ($schedules as $schedule) {
            if (isset($schedule['shift_slots'])) {
                $shifts = $shifts->merge($schedule['shift_slots']);
            }
        }
        
        return $shifts->unique('id')->sortBy('start_time');
    }
}
