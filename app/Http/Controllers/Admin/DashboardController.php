<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin']);
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $selectedWardId = $request->input('ward_id');
        $selectedDate = $request->input('date', now()->format('Y-m-d'));
        $selectedDuration = $request->input('duration', 'daily');
        
        // Get all active wards for the filter dropdown
        $wards = Ward::where('is_active', true)
                     ->with(['hospital', 'specialty'])
                     ->orderBy('name')
                     ->get();
        
        // Get the selected ward if specified
        $selectedWard = $selectedWardId ? Ward::find($selectedWardId) : null;
        
        // Calculate date range based on duration
        $dateRange = $this->calculateDateRange($selectedDate, $selectedDuration);
        
        // Get dashboard data based on filters (for future use)
        $dashboardData = $this->getDashboardData($selectedWardId, $dateRange);
        
        return view('admin.dashboard', compact(
            'wards',
            'selectedWardId',
            'selectedWard',
            'selectedDate',
            'selectedDuration',
            'dashboardData'
        ));
    }
    
    /**
     * Calculate date range based on selected date and duration
     */
    private function calculateDateRange($selectedDate, $duration)
    {
        $date = Carbon::parse($selectedDate);
        
        switch ($duration) {
            case 'weekly':
                return [
                    'start' => $date->startOfWeek()->format('Y-m-d'),
                    'end' => $date->endOfWeek()->format('Y-m-d')
                ];
            case 'monthly':
                return [
                    'start' => $date->startOfMonth()->format('Y-m-d'),
                    'end' => $date->endOfMonth()->format('Y-m-d')
                ];
            case 'daily':
            default:
                return [
                    'start' => $date->format('Y-m-d'),
                    'end' => $date->format('Y-m-d')
                ];
        }
    }
    
    /**
     * Get dashboard data based on filters
     */
    private function getDashboardData($wardId, $dateRange)
    {
        // This method can be expanded to fetch filtered data
        // For now, returning empty array for basic implementation
        return [];
    }
}
