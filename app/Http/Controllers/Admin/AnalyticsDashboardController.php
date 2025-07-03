<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Ward;
use App\Models\Patient;
use App\Models\PatientAdmission;
use App\Models\PatientDischarge;
use App\Models\PatientMovement;
use App\Models\VitalSign;
use App\Models\PatientAlert;
use App\Models\PatientResponse;
use App\Models\PatientSatisfactionSurvey;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboardController extends Controller
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
     * Show the analytics dashboard.
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
        
        // Get all filtered metrics
        $bedOccupancyMetrics = $this->getBedOccupancyMetrics($selectedWardId, $dateRange);
        $patientFlowMetrics = $this->getPatientFlowMetrics($selectedWardId, $dateRange);
        $patientStatusMetrics = $this->getPatientStatusMetrics($selectedWardId, $dateRange);
        $nurseCallMetrics = $this->getNurseCallResponseMetrics($selectedWardId, $dateRange);
        $housekeepingMetrics = $this->getHousekeepingMetrics($selectedWardId, $dateRange);
        $patientFeedbackMetrics = $this->getPatientFeedbackMetrics($selectedWardId, $dateRange);
        $chartData = $this->getChartData($selectedWardId, $dateRange);
        
        return view('admin.analytics-dashboard', compact(
            'bedOccupancyMetrics',
            'patientFlowMetrics', 
            'patientStatusMetrics',
            'nurseCallMetrics',
            'housekeepingMetrics',
            'patientFeedbackMetrics',
            'chartData',
            'wards',
            'selectedWardId',
            'selectedWard',
            'selectedDate',
            'selectedDuration'
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
                    'start' => $date->copy()->startOfWeek(),
                    'end' => $date->copy()->endOfWeek()
                ];
            case 'monthly':
                // For monthly view, always use the full month regardless of the specific day
                return [
                    'start' => $date->copy()->startOfMonth(),
                    'end' => $date->copy()->endOfMonth()
                ];
            case 'daily':
            default:
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay()
                ];
        }
    }

    /**
     * Get bed occupancy metrics
     */
    private function getBedOccupancyMetrics($wardId = null, $dateRange = null)
    {
        // Base query for beds
        $bedsQuery = Bed::query();
        if ($wardId) {
            $bedsQuery->where('ward_id', $wardId);
        }
        
        $totalBeds = $bedsQuery->count();
        $occupiedBeds = $bedsQuery->where('status', 'occupied')->count();
        $availableBeds = $bedsQuery->where('status', 'available')->count();
        $cleaningNeededBeds = $bedsQuery->where('status', 'cleaning_needed')->count();
        $maintenanceBeds = $bedsQuery->where('status', 'maintenance')->count();
        
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
        
        // Calculate average length of stay with filters
        $averageLengthOfStay = $this->calculateAverageLengthOfStay($wardId, $dateRange);
        
        // Calculate bed turnover for the selected date range
        $bedTurnover = $this->calculateBedTurnover($wardId, $dateRange);
        
        // Ward-wise breakdown (only if not filtering by specific ward)
        $wardBreakdown = $this->getWardBreakdown($wardId);

        return [
            'total_beds' => $totalBeds,
            'occupied_beds' => $occupiedBeds,
            'available_beds' => $availableBeds,
            'cleaning_needed_beds' => $cleaningNeededBeds,
            'maintenance_beds' => $maintenanceBeds,
            'occupancy_rate' => $occupancyRate,
            'average_length_of_stay' => $averageLengthOfStay,
            'bed_turnover' => $bedTurnover,
            'ward_breakdown' => $wardBreakdown,
        ];
    }

    /**
     * Get patient flow metrics
     */
    private function getPatientFlowMetrics($wardId = null, $dateRange = null)
    {
        $startDate = $dateRange ? $dateRange['start'] : Carbon::today();
        $endDate = $dateRange ? $dateRange['end'] : Carbon::today();
        
        // Build base queries with ward filter if specified
        $admissionQuery = PatientAdmission::query();
        $dischargeQuery = PatientDischarge::query();
        $movementQuery = PatientMovement::query();
        
        if ($wardId) {
            $admissionQuery->where('ward_id', $wardId);
            $dischargeQuery->where('ward_id', $wardId);
            $movementQuery->whereHas('fromBed.ward', function($q) use ($wardId) {
                $q->where('id', $wardId);
            });
        }
        
        // Get counts for the selected date range
        $admissionsInRange = $admissionQuery->whereBetween('admission_date', [$startDate, $endDate])->count();
        $dischargesInRange = $dischargeQuery->whereBetween('discharge_date', [$startDate, $endDate])->count();
        $transfersInRange = $movementQuery->whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Active movements (patients currently away from beds)
        $activeMovements = $movementQuery->where('status', 'sent')->count();
        
        // Peak hours analysis
        $peakHours = $this->getAdmissionPeakHours($wardId, $dateRange);
        
        return [
            'admissions_in_range' => $admissionsInRange,
            'discharges_in_range' => $dischargesInRange,
            'transfers_in_range' => $transfersInRange,
            'active_movements' => $activeMovements,
            'peak_hours' => $peakHours,
        ];
    }

    /**
     * Get patient status monitoring metrics
     */
    private function getPatientStatusMetrics($wardId = null, $dateRange = null)
    {
        // Base query for current patients
        $patientQuery = Patient::whereHas('bed', function ($query) use ($wardId) {
            $query->where('status', 'occupied');
            if ($wardId) {
                $query->where('ward_id', $wardId);
            }
        });
        
        $totalCurrentPatients = $patientQuery->count();
        
        // Vital signs monitoring within date range
        $startDate = $dateRange ? $dateRange['start'] : Carbon::now()->subHours(24);
        $endDate = $dateRange ? $dateRange['end'] : Carbon::now();
        
        $patientsWithVitalSigns = $patientQuery->whereHas('vitalSigns', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->count();
        
        // Critical patients (EWS >= 7)
        $criticalPatients = $patientQuery->whereHas('latestVitalSigns', function ($query) {
            $query->where('total_ews', '>=', 7);
        })->count();
        
        // High risk patients (EWS 5-6)
        $highRiskPatients = $patientQuery->whereHas('latestVitalSigns', function ($query) {
            $query->whereBetween('total_ews', [5, 6]);
        })->count();
        
        // Active alerts within date range
        $alertQuery = PatientAlert::whereIn('status', ['new', 'seen']);
        if ($wardId) {
            $alertQuery->where('ward_id', $wardId);
        }
        if ($dateRange) {
            $alertQuery->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }
        
        $activeAlerts = $alertQuery->count();
        $urgentAlerts = $alertQuery->where('is_urgent', true)->count();
        
        // Patients by risk factors
        $riskFactorBreakdown = $this->getRiskFactorBreakdown($wardId);
        
        return [
            'total_current_patients' => $totalCurrentPatients,
            'patients_with_recent_vitals' => $patientsWithVitalSigns,
            'critical_patients' => $criticalPatients,
            'high_risk_patients' => $highRiskPatients,
            'active_alerts' => $activeAlerts,
            'urgent_alerts' => $urgentAlerts,
            'risk_factor_breakdown' => $riskFactorBreakdown,
        ];
    }

    /**
     * Calculate average length of stay
     */
    private function calculateAverageLengthOfStay($wardId = null, $dateRange = null)
    {
        // Get discharged patients from date range or last 30 days
        $startDate = $dateRange ? $dateRange['start'] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange['end'] : Carbon::now();
        
        $dischargeQuery = PatientDischarge::with('patient');
        
        if ($wardId) {
            $dischargeQuery->where('ward_id', $wardId);
        }
        
        $recentDischarges = $dischargeQuery->whereBetween('discharge_date', [$startDate, $endDate])->get();
        
        if ($recentDischarges->isEmpty()) {
            return 0;
        }
        
        $totalDays = 0;
        $validDischarges = 0;
        
        foreach ($recentDischarges as $discharge) {
            // Find the matching admission
            $admission = PatientAdmission::where('patient_id', $discharge->patient_id)
                ->where('ward_id', $discharge->ward_id)
                ->where('bed_number', $discharge->bed_number)
                ->where('admission_date', '<', $discharge->discharge_date)
                ->orderBy('admission_date', 'desc')
                ->first();
            
            if ($admission) {
                $days = Carbon::parse($discharge->discharge_date)->diffInDays(Carbon::parse($admission->admission_date));
                $totalDays += $days;
                $validDischarges++;
            }
        }
        
        return $validDischarges > 0 ? round($totalDays / $validDischarges, 1) : 0;
    }

    /**
     * Get admission peak hours
     */
    private function getAdmissionPeakHours($wardId = null, $dateRange = null)
    {
        $startDate = $dateRange ? $dateRange['start'] : Carbon::now()->subDays(7);
        $endDate = $dateRange ? $dateRange['end'] : Carbon::now();
        
        $admissionQuery = PatientAdmission::whereBetween('admission_date', [$startDate, $endDate]);
        
        if ($wardId) {
            $admissionQuery->where('ward_id', $wardId);
        }
        
        $admissions = $admissionQuery->select(DB::raw('HOUR(admission_date) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();
        
        return $admissions->take(3)->pluck('hour')->toArray();
    }

    /**
     * Get risk factor breakdown
     */
    private function getRiskFactorBreakdown($wardId = null)
    {
        $admissionQuery = PatientAdmission::where('is_active', true);
        
        if ($wardId) {
            $admissionQuery->where('ward_id', $wardId);
        }
        
        $activeAdmissions = $admissionQuery->get();
        
        $riskFactors = [
            'fallrisk' => 0,
            'dnr' => 0,
            'intubated' => 0,
            'isolation' => 0,
        ];
        
        foreach ($activeAdmissions as $admission) {
            if (!empty($admission->risk_factors)) {
                foreach ($admission->risk_factors as $factor) {
                    if (isset($riskFactors[$factor])) {
                        $riskFactors[$factor]++;
                    }
                }
            }
        }
        
        return $riskFactors;
    }

    /**
     * Get chart data for visualizations
     */
    private function getChartData($wardId = null, $dateRange = null)
    {
        // Chart data based on date range or last 7 days
        $startDate = $dateRange ? $dateRange['start'] : Carbon::now()->subDays(6);
        $endDate = $dateRange ? $dateRange['end'] : Carbon::now();
        
        $chartData = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $admissionQuery = PatientAdmission::whereDate('admission_date', $currentDate);
            $dischargeQuery = PatientDischarge::whereDate('discharge_date', $currentDate);
            
            if ($wardId) {
                $admissionQuery->where('ward_id', $wardId);
                $dischargeQuery->where('ward_id', $wardId);
            }
            
            $chartData[] = [
                'date' => $currentDate->format('M j'),
                'admissions' => $admissionQuery->count(),
                'discharges' => $dischargeQuery->count(),
            ];
            
            $currentDate->addDay();
        }
        
        // Bed status distribution
        $bedQuery = Bed::query();
        if ($wardId) {
            $bedQuery->where('ward_id', $wardId);
        }
        
        $bedStatusDistribution = [
            'occupied' => $bedQuery->where('status', 'occupied')->count(),
            'available' => $bedQuery->where('status', 'available')->count(),
            'cleaning_needed' => $bedQuery->where('status', 'cleaning_needed')->count(),
            'maintenance' => $bedQuery->where('status', 'maintenance')->count(),
        ];
        
        return [
            'chart_data' => $chartData,
            'bed_status_distribution' => $bedStatusDistribution,
        ];
    }

    /**
     * Get nurse call response time metrics
     */
    private function getNurseCallResponseMetrics($wardId = null, $dateRange = null)
    {
        // Get all alerts from last 30 days that have been resolved (with responses)
        $resolvedAlerts = PatientAlert::where('status', 'resolved')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->with('responses')
            ->get();

        $totalResponseTime = 0;
        $urgentResponseTime = 0;
        $nonUrgentResponseTime = 0;
        $urgentCount = 0;
        $nonUrgentCount = 0;
        $validResponseCount = 0;

        // Get response time breakdown by categories
        $responseTimeBreakdown = [
            'under_5_min' => 0,
            '5_to_15_min' => 0,
            '15_to_30_min' => 0,
            'over_30_min' => 0,
        ];

        foreach ($resolvedAlerts as $alert) {
            // Get the first response time (when staff first responded)
            $firstResponse = $alert->responses()->orderBy('created_at')->first();
            
            if ($firstResponse) {
                $responseTime = Carbon::parse($firstResponse->created_at)->diffInMinutes(Carbon::parse($alert->created_at));
                $totalResponseTime += $responseTime;
                $validResponseCount++;

                if ($alert->is_urgent) {
                    $urgentResponseTime += $responseTime;
                    $urgentCount++;
                } else {
                    $nonUrgentResponseTime += $responseTime;
                    $nonUrgentCount++;
                }

                // Categorize response time
                if ($responseTime < 5) {
                    $responseTimeBreakdown['under_5_min']++;
                } elseif ($responseTime < 15) {
                    $responseTimeBreakdown['5_to_15_min']++;
                } elseif ($responseTime < 30) {
                    $responseTimeBreakdown['15_to_30_min']++;
                } else {
                    $responseTimeBreakdown['over_30_min']++;
                }
            }
        }

        $averageResponseTime = $validResponseCount > 0 ? round($totalResponseTime / $validResponseCount, 1) : 0;
        $averageUrgentResponseTime = $urgentCount > 0 ? round($urgentResponseTime / $urgentCount, 1) : 0;
        $averageNonUrgentResponseTime = $nonUrgentCount > 0 ? round($nonUrgentResponseTime / $nonUrgentCount, 1) : 0;

        // Get today's metrics
        $todayAlerts = PatientAlert::whereDate('created_at', Carbon::today())->count();
        $todayResolved = PatientAlert::whereDate('created_at', Carbon::today())
            ->where('status', 'resolved')->count();
        $todayPending = PatientAlert::whereDate('created_at', Carbon::today())
            ->whereIn('status', ['new', 'seen'])->count();

        return [
            'total_alerts_30_days' => PatientAlert::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'responded_alerts_30_days' => $validResponseCount,
            'average_response_time' => $averageResponseTime,
            'average_urgent_response_time' => $averageUrgentResponseTime,
            'average_non_urgent_response_time' => $averageNonUrgentResponseTime,
            'response_time_breakdown' => $responseTimeBreakdown,
            'today_alerts' => $todayAlerts,
            'today_responded' => $todayResolved,
            'today_pending' => $todayPending,
        ];
    }

    /**
     * Get housekeeping and environment metrics
     */
    private function getHousekeepingMetrics($wardId = null, $dateRange = null)
    {
        // Calculate room cleaning turnaround time
        // For this, we need to track when beds go from 'cleaning_needed' to 'available'
        // Since we don't have a specific log table, we'll estimate based on current status and patterns
        
        $cleaningNeededBeds = Bed::where('status', 'cleaning_needed')->count();
        $totalBeds = Bed::count();
        
        // Get recent discharge data to estimate cleaning workload
        $recentDischarges = PatientDischarge::where('discharge_date', '>=', Carbon::now()->subDays(7))->count();
        $dailyCleaningNeeds = $recentDischarges / 7;
        
        // Estimate average cleaning time based on bed status changes
        // This is a simplified calculation - in real implementation, you'd track status changes in a log table
        $estimatedCleaningTime = 45; // minutes (can be adjusted based on hospital standards)
        
        // Calculate cleaning efficiency metrics
        $cleaningBacklog = $cleaningNeededBeds;
        $cleaningEfficiency = $totalBeds > 0 ? round((($totalBeds - $cleaningNeededBeds) / $totalBeds) * 100, 1) : 100;
        
        // Ward-wise cleaning status
        $wardCleaningStatus = Ward::with('beds')->get()->map(function ($ward) {
            $wardBeds = $ward->beds;
            $cleaningNeeded = $wardBeds->where('status', 'cleaning_needed')->count();
            $available = $wardBeds->where('status', 'available')->count();
            $total = $wardBeds->count();
            
            return [
                'ward_name' => $ward->name,
                'cleaning_needed' => $cleaningNeeded,
                'available' => $available,
                'total' => $total,
                'cleaning_percentage' => $total > 0 ? round(($cleaningNeeded / $total) * 100, 1) : 0,
            ];
        });

        // Environmental safety metrics
        $maintenanceBeds = Bed::where('status', 'maintenance')->count();
        $availabilityRate = $totalBeds > 0 ? round((($totalBeds - $maintenanceBeds - $cleaningNeededBeds) / $totalBeds) * 100, 1) : 0;

        return [
            'cleaning_needed_beds' => $cleaningNeededBeds,
            'estimated_cleaning_time' => $estimatedCleaningTime,
            'daily_cleaning_needs' => round($dailyCleaningNeeds, 1),
            'cleaning_backlog' => $cleaningBacklog,
            'cleaning_efficiency' => $cleaningEfficiency,
            'ward_cleaning_status' => $wardCleaningStatus,
            'maintenance_beds' => $maintenanceBeds,
            'room_availability_rate' => $availabilityRate,
            'total_beds' => $totalBeds,
        ];
    }

    /**
     * Get patient feedback metrics
     */
    private function getPatientFeedbackMetrics($wardId = null, $dateRange = null)
    {
        // Build base query with ward filter if specified
        $surveyQuery = PatientSatisfactionSurvey::with(['patient', 'ward']);
        
        if ($wardId) {
            $surveyQuery->where('ward_id', $wardId);
        }
        
        // Get satisfaction surveys from last 30 days
        $recentSurveys = $surveyQuery->where('created_at', '>=', Carbon::now()->subDays(30))->get();
        
        // Calculate satisfaction metrics
        $totalResponses = $recentSurveys->count();
        $positiveResponses = $recentSurveys->where('response_type', 'positive')->count();
        $negativeResponses = $recentSurveys->where('response_type', 'negative')->count();
        $neutralResponses = $recentSurveys->where('response_type', 'neutral')->count();
        
        $satisfactionRate = $totalResponses > 0 ? round(($positiveResponses / $totalResponses) * 100, 1) : 0;
        
        // Calculate average ratings by category
        $careRatingAvg = $recentSurveys->whereNotNull('care_rating')->avg('care_rating');
        $staffRatingAvg = $recentSurveys->whereNotNull('staff_rating')->avg('staff_rating');
        $cleanRatingAvg = $recentSurveys->whereNotNull('clean_rating')->avg('clean_rating');
        $commRatingAvg = $recentSurveys->whereNotNull('comm_rating')->avg('comm_rating');
        
        // Count responses by rating category 
        $feedbackCategories = [
            'care_quality' => $recentSurveys->whereNotNull('care_rating')->count(),
            'staff_behavior' => $recentSurveys->whereNotNull('staff_rating')->count(),
            'room_comfort' => $recentSurveys->whereNotNull('clean_rating')->count(),
            'communication' => $recentSurveys->whereNotNull('comm_rating')->count(),
            'food_service' => 0, // Not tracked in current survey
        ];
        
        // Get recent feedback surveys with comments
        $recentFeedback = PatientSatisfactionSurvey::with(['patient', 'ward'])
            ->where('created_at', '>=', Carbon::now()->subDays(7));
            
        if ($wardId) {
            $recentFeedback->where('ward_id', $wardId);
        }
        
        $recentFeedback = $recentFeedback->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($survey) {
                return (object) [
                    'id' => $survey->id,
                    'created_at' => $survey->created_at,
                    'patient' => $survey->patient,
                    'ward' => $survey->ward,
                    'response_type' => $survey->response_type,
                    'overall_rating' => $survey->overall_rating,
                    'message' => $survey->comments,
                    'category' => $survey->category,
                ];
            });
        
        // Calculate response rate (surveys vs total patients discharged in last 30 days)
        $totalDischargedPatients = PatientDischarge::where('discharge_date', '>=', Carbon::now()->subDays(30));
        if ($wardId) {
            $totalDischargedPatients->where('ward_id', $wardId);
        }
        $totalDischargedPatients = $totalDischargedPatients->count();
        
        $responseRate = $totalDischargedPatients > 0 ? round(($totalResponses / $totalDischargedPatients) * 100, 1) : 0;
        
        // Get sentiment trend over last 7 days
        $sentimentTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $daySurveyQuery = PatientSatisfactionSurvey::whereDate('created_at', $date);
            
            if ($wardId) {
                $daySurveyQuery->where('ward_id', $wardId);
            }
            
            $daySurveys = $daySurveyQuery->get();
            $dayPositive = $daySurveys->where('response_type', 'positive')->count();
            $dayTotal = $daySurveys->count();
            
            $sentimentTrend[] = [
                'date' => $date->format('M j'),
                'satisfaction_rate' => $dayTotal > 0 ? round(($dayPositive / $dayTotal) * 100, 1) : 0,
                'total_responses' => $dayTotal,
            ];
        }

        return [
            'total_responses_30_days' => $totalResponses,
            'positive_responses' => $positiveResponses,
            'negative_responses' => $negativeResponses,
            'neutral_responses' => $neutralResponses,
            'satisfaction_rate' => $satisfactionRate,
            'response_rate' => $responseRate,
            'feedback_categories' => $feedbackCategories,
            'recent_feedback' => $recentFeedback,
            'sentiment_trend' => $sentimentTrend,
            'average_ratings' => [
                'care_rating' => $careRatingAvg ? round($careRatingAvg, 2) : 0,
                'staff_rating' => $staffRatingAvg ? round($staffRatingAvg, 2) : 0,
                'clean_rating' => $cleanRatingAvg ? round($cleanRatingAvg, 2) : 0,
                'comm_rating' => $commRatingAvg ? round($commRatingAvg, 2) : 0,
            ],
        ];
    }

    /**
     * Get ward breakdown
     */
    private function getWardBreakdown($wardId = null)
    {
        $wardBreakdown = [];
        $wards = Ward::with('beds')->get();
        
        foreach ($wards as $ward) {
            $wardBeds = $ward->beds;
            $occupied = $wardBeds->where('status', 'occupied')->count();
            $total = $wardBeds->count();
            
            // Show all wards if no specific ward is selected, or just the selected ward
            if ($wardId === null || $wardId == $ward->id) {
                $wardBreakdown[] = [
                    'ward_name' => $ward->name,
                    'total_beds' => $total,
                    'occupied_beds' => $occupied,
                    'available_beds' => $wardBeds->where('status', 'available')->count(),
                    'cleaning_needed' => $wardBeds->where('status', 'cleaning_needed')->count(),
                    'occupancy_rate' => $total > 0 ? round(($occupied / $total) * 100, 1) : 0,
                ];
            }
        }
        
        return $wardBreakdown;
    }

    /**
     * Calculate bed turnover
     */
    private function calculateBedTurnover($wardId = null, $dateRange = null)
    {
        $startDate = $dateRange ? $dateRange['start'] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange['end'] : Carbon::now();
        
        $dischargeQuery = PatientDischarge::whereBetween('discharge_date', [$startDate, $endDate]);
        
        if ($wardId) {
            $dischargeQuery->where('ward_id', $wardId);
        }
        
        $dischargesInRange = $dischargeQuery->count();
        
        $bedQuery = Bed::query();
        if ($wardId) {
            $bedQuery->where('ward_id', $wardId);
        }
        
        $totalBeds = $bedQuery->count();
        
        $bedTurnover = $totalBeds > 0 ? round($dischargesInRange / $totalBeds, 2) : 0;
        
        return $bedTurnover;
    }
} 