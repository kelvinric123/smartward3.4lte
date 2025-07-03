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
    public function index()
    {
        // Get all basic metrics
        $bedOccupancyMetrics = $this->getBedOccupancyMetrics();
        $patientFlowMetrics = $this->getPatientFlowMetrics();
        $patientStatusMetrics = $this->getPatientStatusMetrics();
        $nurseCallMetrics = $this->getNurseCallResponseMetrics();
        $housekeepingMetrics = $this->getHousekeepingMetrics();
        $patientFeedbackMetrics = $this->getPatientFeedbackMetrics();
        $chartData = $this->getChartData();
        
        return view('admin.analytics-dashboard', compact(
            'bedOccupancyMetrics',
            'patientFlowMetrics', 
            'patientStatusMetrics',
            'nurseCallMetrics',
            'housekeepingMetrics',
            'patientFeedbackMetrics',
            'chartData'
        ));
    }

    /**
     * Get bed occupancy metrics
     */
    private function getBedOccupancyMetrics()
    {
        $totalBeds = Bed::count();
        $occupiedBeds = Bed::where('status', 'occupied')->count();
        $availableBeds = Bed::where('status', 'available')->count();
        $cleaningNeededBeds = Bed::where('status', 'cleaning_needed')->count();
        $maintenanceBeds = Bed::where('status', 'maintenance')->count();
        
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
        
        // Calculate average length of stay
        $averageLengthOfStay = $this->calculateAverageLengthOfStay();
        
        // Calculate bed turnover (discharges in last 30 days / total beds)
        $dischargesLast30Days = PatientDischarge::where('discharge_date', '>=', Carbon::now()->subDays(30))->count();
        $bedTurnover = $totalBeds > 0 ? round($dischargesLast30Days / $totalBeds, 2) : 0;
        
        // Ward-wise breakdown
        $wardBreakdown = Ward::with('beds')->get()->map(function ($ward) {
            $wardBeds = $ward->beds;
            $occupied = $wardBeds->where('status', 'occupied')->count();
            $total = $wardBeds->count();
            
            return [
                'ward_name' => $ward->name,
                'total_beds' => $total,
                'occupied_beds' => $occupied,
                'available_beds' => $wardBeds->where('status', 'available')->count(),
                'cleaning_needed' => $wardBeds->where('status', 'cleaning_needed')->count(),
                'occupancy_rate' => $total > 0 ? round(($occupied / $total) * 100, 1) : 0,
            ];
        });

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
    private function getPatientFlowMetrics()
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Admissions
        $admissionsToday = PatientAdmission::whereDate('admission_date', $today)->count();
        $admissionsThisWeek = PatientAdmission::where('admission_date', '>=', $thisWeek)->count();
        $admissionsThisMonth = PatientAdmission::where('admission_date', '>=', $thisMonth)->count();
        
        // Discharges
        $dischargesToday = PatientDischarge::whereDate('discharge_date', $today)->count();
        $dischargesThisWeek = PatientDischarge::where('discharge_date', '>=', $thisWeek)->count();
        $dischargesThisMonth = PatientDischarge::where('discharge_date', '>=', $thisMonth)->count();
        
        // Transfers (patient movements)
        $transfersToday = PatientMovement::whereDate('created_at', $today)->count();
        $transfersThisWeek = PatientMovement::where('created_at', '>=', $thisWeek)->count();
        $transfersThisMonth = PatientMovement::where('created_at', '>=', $thisMonth)->count();
        
        // Active movements (patients currently away from beds)
        $activeMovements = PatientMovement::where('status', 'sent')->count();
        
        // Peak hours analysis (last 7 days)
        $peakHours = $this->getAdmissionPeakHours();
        
        return [
            'admissions' => [
                'today' => $admissionsToday,
                'this_week' => $admissionsThisWeek,
                'this_month' => $admissionsThisMonth,
            ],
            'discharges' => [
                'today' => $dischargesToday,
                'this_week' => $dischargesThisWeek,
                'this_month' => $dischargesThisMonth,
            ],
            'transfers' => [
                'today' => $transfersToday,
                'this_week' => $transfersThisWeek,
                'this_month' => $transfersThisMonth,
            ],
            'active_movements' => $activeMovements,
            'peak_hours' => $peakHours,
        ];
    }

    /**
     * Get patient status monitoring metrics
     */
    private function getPatientStatusMetrics()
    {
        // Current patients
        $totalCurrentPatients = Patient::whereHas('bed', function ($query) {
            $query->where('status', 'occupied');
        })->count();
        
        // Vital signs monitoring
        $patientsWithVitalSigns = Patient::whereHas('vitalSigns', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subHours(24));
        })->count();
        
        // Critical patients (EWS >= 7)
        $criticalPatients = Patient::whereHas('latestVitalSigns', function ($query) {
            $query->where('total_ews', '>=', 7);
        })->count();
        
        // High risk patients (EWS 5-6)
        $highRiskPatients = Patient::whereHas('latestVitalSigns', function ($query) {
            $query->whereBetween('total_ews', [5, 6]);
        })->count();
        
        // Active alerts
        $activeAlerts = PatientAlert::whereIn('status', ['new', 'seen'])->count();
        $urgentAlerts = PatientAlert::whereIn('status', ['new', 'seen'])->where('is_urgent', true)->count();
        
        // Patients by risk factors
        $riskFactorBreakdown = $this->getRiskFactorBreakdown();
        
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
    private function calculateAverageLengthOfStay()
    {
        // Get discharged patients from last 30 days
        $recentDischarges = PatientDischarge::with('patient')
            ->where('discharge_date', '>=', Carbon::now()->subDays(30))
            ->get();
        
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
    private function getAdmissionPeakHours()
    {
        $admissions = PatientAdmission::where('admission_date', '>=', Carbon::now()->subDays(7))
            ->select(DB::raw('HOUR(admission_date) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();
        
        return $admissions->take(3)->pluck('hour')->toArray();
    }

    /**
     * Get risk factor breakdown
     */
    private function getRiskFactorBreakdown()
    {
        $activeAdmissions = PatientAdmission::where('is_active', true)->get();
        
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
    private function getChartData()
    {
        // Last 7 days admission/discharge data
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $admissions = PatientAdmission::whereDate('admission_date', $date)->count();
            $discharges = PatientDischarge::whereDate('discharge_date', $date)->count();
            
            $last7Days[] = [
                'date' => $date->format('M j'),
                'admissions' => $admissions,
                'discharges' => $discharges,
            ];
        }
        
        // Bed status distribution
        $bedStatusDistribution = [
            'occupied' => Bed::where('status', 'occupied')->count(),
            'available' => Bed::where('status', 'available')->count(),
            'cleaning_needed' => Bed::where('status', 'cleaning_needed')->count(),
            'maintenance' => Bed::where('status', 'maintenance')->count(),
        ];
        
        return [
            'last_7_days' => $last7Days,
            'bed_status_distribution' => $bedStatusDistribution,
        ];
    }

    /**
     * Get nurse call response time metrics
     */
    private function getNurseCallResponseMetrics()
    {
        // Get all alerts from last 30 days that have been responded to
        $respondedAlerts = PatientAlert::where('status', 'responded')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereNotNull('responded_at')
            ->get();

        $totalResponseTime = 0;
        $urgentResponseTime = 0;
        $nonUrgentResponseTime = 0;
        $urgentCount = 0;
        $nonUrgentCount = 0;

        foreach ($respondedAlerts as $alert) {
            $responseTime = Carbon::parse($alert->responded_at)->diffInMinutes(Carbon::parse($alert->created_at));
            $totalResponseTime += $responseTime;

            if ($alert->is_urgent) {
                $urgentResponseTime += $responseTime;
                $urgentCount++;
            } else {
                $nonUrgentResponseTime += $responseTime;
                $nonUrgentCount++;
            }
        }

        $averageResponseTime = $respondedAlerts->count() > 0 ? round($totalResponseTime / $respondedAlerts->count(), 1) : 0;
        $averageUrgentResponseTime = $urgentCount > 0 ? round($urgentResponseTime / $urgentCount, 1) : 0;
        $averageNonUrgentResponseTime = $nonUrgentCount > 0 ? round($nonUrgentResponseTime / $nonUrgentCount, 1) : 0;

        // Get response time breakdown by categories
        $responseTimeBreakdown = [
            'under_5_min' => 0,
            '5_to_15_min' => 0,
            '15_to_30_min' => 0,
            'over_30_min' => 0,
        ];

        foreach ($respondedAlerts as $alert) {
            $responseTime = Carbon::parse($alert->responded_at)->diffInMinutes(Carbon::parse($alert->created_at));
            
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

        // Get today's metrics
        $todayAlerts = PatientAlert::whereDate('created_at', Carbon::today())->count();
        $todayResponded = PatientAlert::whereDate('created_at', Carbon::today())
            ->where('status', 'responded')->count();
        $todayPending = PatientAlert::whereDate('created_at', Carbon::today())
            ->whereIn('status', ['new', 'seen'])->count();

        return [
            'total_alerts_30_days' => PatientAlert::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'responded_alerts_30_days' => $respondedAlerts->count(),
            'average_response_time' => $averageResponseTime,
            'average_urgent_response_time' => $averageUrgentResponseTime,
            'average_non_urgent_response_time' => $averageNonUrgentResponseTime,
            'response_time_breakdown' => $responseTimeBreakdown,
            'today_alerts' => $todayAlerts,
            'today_responded' => $todayResponded,
            'today_pending' => $todayPending,
        ];
    }

    /**
     * Get housekeeping and environment metrics
     */
    private function getHousekeepingMetrics()
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
    private function getPatientFeedbackMetrics()
    {
        // Get patient responses from last 30 days
        $recentResponses = PatientResponse::where('created_at', '>=', Carbon::now()->subDays(30))->get();
        
        // Calculate satisfaction metrics
        $totalResponses = $recentResponses->count();
        $positiveResponses = $recentResponses->where('response_type', 'positive')->count();
        $negativeResponses = $recentResponses->where('response_type', 'negative')->count();
        $neutralResponses = $recentResponses->where('response_type', 'neutral')->count();
        
        $satisfactionRate = $totalResponses > 0 ? round(($positiveResponses / $totalResponses) * 100, 1) : 0;
        
        // Get feedback by category (assuming there's a category field)
        $feedbackCategories = [
            'care_quality' => $recentResponses->where('category', 'care_quality')->count(),
            'food_service' => $recentResponses->where('category', 'food_service')->count(),
            'room_comfort' => $recentResponses->where('category', 'room_comfort')->count(),
            'staff_behavior' => $recentResponses->where('category', 'staff_behavior')->count(),
            'communication' => $recentResponses->where('category', 'communication')->count(),
        ];
        
        // Get recent feedback responses
        $recentFeedback = PatientResponse::with(['patient'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Calculate response rate (responses vs total patients)
        $totalCurrentPatients = Patient::whereHas('bed', function ($query) {
            $query->where('status', 'occupied');
        })->count();
        
        $responseRate = $totalCurrentPatients > 0 ? round(($totalResponses / $totalCurrentPatients) * 100, 1) : 0;
        
        // Get sentiment trend over last 7 days
        $sentimentTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayResponses = PatientResponse::whereDate('created_at', $date)->get();
            $dayPositive = $dayResponses->where('response_type', 'positive')->count();
            $dayTotal = $dayResponses->count();
            
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
        ];
    }
} 