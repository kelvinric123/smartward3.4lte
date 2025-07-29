<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    /**
     * Get the current shift based on current time
     */
    public function getCurrentShift()
    {
        $currentTime = now()->format('H:i');
        $shifts = $this->getShiftSlots();
        
        foreach ($shifts as $shift) {
            $startTime = $shift['start_time'];
            $endTime = $shift['end_time'];
            
            // Handle overnight shifts (end time < start time)
            if ($endTime < $startTime) {
                // Overnight shift (e.g., 21:00 - 07:30)
                if ($currentTime >= $startTime || $currentTime <= $endTime) {
                    return $shift;
                }
            } else {
                // Regular shift (e.g., 07:00 - 14:30)
                if ($currentTime >= $startTime && $currentTime <= $endTime) {
                    return $shift;
                }
            }
        }
        
        return null;
    }

    /**
     * Get nurses on duty for current shift
     */
    public function getCurrentShiftNurses()
    {
        $currentShift = $this->getCurrentShift();
        if (!$currentShift) {
            return collect();
        }
        
        $today = now()->format('Y-m-d');
        $todayAssignments = $this->getScheduleForDate($today);
        
        return $todayAssignments->get($currentShift['name'], collect());
    }

    /**
     * Count unique nurses on duty for current shift
     */
    public function getCurrentShiftNursesCount()
    {
        $currentShift = $this->getCurrentShift();
        if (!$currentShift) {
            return 0;
        }
        
        $today = now()->format('Y-m-d');
        $todayAssignments = $this->getScheduleForDate($today);
        $currentShiftNurses = $todayAssignments->get($currentShift['name'], collect());
        
        // Get unique nurses by employee_id
        $uniqueNurses = collect();
        foreach ($currentShiftNurses as $assignment) {
            if (isset($assignment['member']['employee_id'])) {
                $uniqueNurses->put($assignment['member']['employee_id'], $assignment['member']);
            }
        }
        
        return $uniqueNurses->count();
    }

    /**
     * Get all nurses on duty for today (all shifts)
     */
    public function getTodayNursesCount()
    {
        $todayAssignments = $this->getCurrentNurses();
        
        // Get unique nurses by employee_id across all shifts
        $uniqueNurses = collect();
        foreach ($todayAssignments as $shiftName => $nurses) {
            foreach ($nurses as $assignment) {
                if (isset($assignment['member']['employee_id'])) {
                    $uniqueNurses->put($assignment['member']['employee_id'], $assignment['member']);
                }
            }
        }
        
        return $uniqueNurses->count();
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
