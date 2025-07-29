<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NurseSchedule;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NurseScheduleController extends Controller
{
    public function index()
    {
        $schedules = NurseSchedule::with('ward')->orderBy('created_at', 'desc')->get();
        $wards = Ward::all();
        
        return view('admin.integration.nurse-schedule.index', compact('schedules', 'wards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ward_id' => 'nullable|exists:wards,id',
            'schedule_file' => 'required|file|mimes:json|max:10240', // 10MB max
        ]);

        $file = $request->file('schedule_file');
        $content = file_get_contents($file->getPathname());
        $scheduleData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['schedule_file' => 'Invalid JSON file format.']);
        }

        // Extract schedule dates from the JSON data
        $startDate = null;
        $endDate = null;
        
        if (isset($scheduleData['roster_info']['start_date'])) {
            $startDate = $scheduleData['roster_info']['start_date'];
        }
        
        if (isset($scheduleData['roster_info']['end_date'])) {
            $endDate = $scheduleData['roster_info']['end_date'];
        }

        NurseSchedule::create([
            'name' => $request->name,
            'ward_id' => $request->ward_id,
            'schedule_data' => $scheduleData,
            'original_filename' => $file->getClientOriginalName(),
            'schedule_start_date' => $startDate,
            'schedule_end_date' => $endDate,
            'is_active' => true,
        ]);

        return back()->with('success', 'Nurse schedule uploaded successfully!');
    }

    public function show($id)
    {
        $schedule = NurseSchedule::with('ward')->findOrFail($id);
        return view('admin.integration.nurse-schedule.show', compact('schedule'));
    }

    public function iframe($id)
    {
        $schedule = NurseSchedule::with('ward')->findOrFail($id);
        $date = request()->get('date', now()->format('Y-m-d'));
        
        // Get assignments for the specified date using the model helper
        $dateAssignments = $schedule->getScheduleForDate($date);
        
        // Get current shift information if viewing today's schedule
        $currentShift = null;
        $currentShiftNurses = collect();
        if ($date === now()->format('Y-m-d')) {
            $currentShift = $schedule->getCurrentShift();
            $currentShiftNurses = $schedule->getCurrentShiftNurses();
        }

        return view('admin.integration.nurse-schedule.iframe', [
            'schedule' => $schedule,
            'todayAssignments' => $dateAssignments,
            'today' => $date,
            'currentShift' => $currentShift,
            'currentShiftNurses' => $currentShiftNurses
        ]);
    }

    public function wardNurses($wardId)
    {
        $ward = Ward::findOrFail($wardId);
        $date = request()->get('date', now()->format('Y-m-d'));
        
        // Find the schedule that covers the requested date
        $activeSchedule = NurseSchedule::where('ward_id', $wardId)
            ->where('is_active', true)
            ->where('schedule_start_date', '<=', $date)
            ->where('schedule_end_date', '>=', $date)
            ->orderBy('created_at', 'desc')
            ->first();

        // If no schedule covers the requested date, try the most recent active schedule
        if (!$activeSchedule) {
            $activeSchedule = NurseSchedule::where('ward_id', $wardId)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$activeSchedule) {
            return view('admin.integration.nurse-schedule.iframe', [
                'schedule' => null,
                'todayAssignments' => collect(),
                'today' => $date,
                'ward' => $ward,
                'currentShift' => null,
                'currentShiftNurses' => collect()
            ]);
        }

        // Get assignments for the specified date using the model helper
        $dateAssignments = $activeSchedule->getScheduleForDate($date);
        
        // Get current shift information if viewing today's schedule
        $currentShift = null;
        $currentShiftNurses = collect();
        if ($date === now()->format('Y-m-d')) {
            $currentShift = $activeSchedule->getCurrentShift();
            $currentShiftNurses = $activeSchedule->getCurrentShiftNurses();
        }

        return view('admin.integration.nurse-schedule.iframe', [
            'schedule' => $activeSchedule,
            'todayAssignments' => $dateAssignments,
            'today' => $date,
            'ward' => $ward,
            'currentShift' => $currentShift,
            'currentShiftNurses' => $currentShiftNurses
        ]);
    }

    public function toggle($id)
    {
        $schedule = NurseSchedule::findOrFail($id);
        $schedule->update(['is_active' => !$schedule->is_active]);
        
        return back()->with('success', 'Schedule status updated successfully!');
    }

    public function destroy($id)
    {
        $schedule = NurseSchedule::findOrFail($id);
        $schedule->delete();
        
        return back()->with('success', 'Schedule deleted successfully!');
    }
}
