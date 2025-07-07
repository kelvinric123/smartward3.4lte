<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Hospital\DashboardController as HospitalDashboardController;
use App\Http\Controllers\Consultant\DashboardController as ConsultantDashboardController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Booking\DashboardController as BookingDashboardController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\ConsultantController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\VitalSignController;
use App\Http\Controllers\Admin\PatientDischargeController;
use App\Http\Controllers\Admin\PatientMovementController;
use App\Http\Controllers\Admin\PatientReferralController;
use App\Http\Controllers\Admin\PatientPanelController;
use App\Http\Controllers\Admin\FoodOrderController;
use App\Http\Controllers\Admin\FoodMenuController;
use App\Http\Controllers\Admin\AnalyticsDashboardController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Auth::routes();

// Default home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AnalyticsDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('hospitals', App\Http\Controllers\Admin\HospitalController::class)->names('admin.hospitals');
    Route::resource('specialties', SpecialtyController::class)->names('admin.specialties');
    Route::resource('consultants', ConsultantController::class)->names('admin.consultants');
    Route::resource('nurses', App\Http\Controllers\Admin\NurseController::class)->names('admin.nurses');
    
    // Patient Search Route - Must be placed before resource route to avoid binding issues
    Route::get('patients/search', [PatientController::class, 'search'])->name('admin.patients.search');
    
    // Patient Resource Routes
    Route::resource('patients', PatientController::class)->names('admin.patients');
    
    // Patient Discharge Routes
    Route::get('patients/{patientId}/discharge', [PatientDischargeController::class, 'create'])->name('admin.patients.discharge');
    Route::post('patients/{patientId}/discharge', [PatientDischargeController::class, 'store'])->name('admin.patients.discharge.store');
    Route::post('patients/{patientId}/discharge/quick', [PatientDischargeController::class, 'quickDischarge'])->name('admin.patients.discharge.quick');
    Route::get('patients/{patientId}/discharge-history', [PatientDischargeController::class, 'history'])->name('admin.patients.discharge.history');
    Route::get('admission-history', [PatientDischargeController::class, 'admissionHistory'])->name('admin.admission.history');
    
    // Movement Actions
    Route::prefix('movements')->name('movements.')->group(function () {
        Route::put('{movement}/send', [PatientMovementController::class, 'sendPatient'])->name('send');
        Route::put('{movement}/return', [PatientMovementController::class, 'returnPatient'])->name('return');
        Route::put('{movement}/cancel', [PatientMovementController::class, 'cancelMovement'])->name('cancel');
    });
    
    // Referral Actions
    Route::prefix('referrals')->group(function () {
        Route::get('consultants-by-specialty', [PatientReferralController::class, 'getConsultantsBySpecialty'])->name('admin.referrals.consultants-by-specialty');
        Route::put('{referral}/status', [PatientReferralController::class, 'updateStatus'])->name('admin.referrals.update-status');
    });
    
    // Patient Referral Routes
    Route::post('patients/{patientId}/referral', [PatientReferralController::class, 'store'])->name('admin.patients.referral.store');
    
    // Specialty Actions
    Route::prefix('specialties')->group(function () {
        Route::get('/by-hospital', [SpecialtyController::class, 'getSpecialtiesByHospital'])->name('admin.specialties.by-hospital');
    });
    
    // Vital Signs Routes
    Route::get('/vital-signs', [VitalSignController::class, 'index'])->name('admin.vital-signs.index');
    Route::get('/vital-signs/check-updates', [VitalSignController::class, 'checkUpdates'])->name('admin.vital-signs.check-updates');
    Route::get('/vital-signs/create', [VitalSignController::class, 'create'])->name('admin.vital-signs.create');
    Route::get('/vital-signs/{vitalSign}', [VitalSignController::class, 'show'])->name('admin.vital-signs.show');
    Route::post('/vital-signs', [VitalSignController::class, 'store'])->name('admin.vital-signs.store');
    Route::get('/vital-signs/{vitalSign}/edit', [VitalSignController::class, 'edit'])->name('admin.vital-signs.edit');
    Route::put('/vital-signs/{vitalSign}', [VitalSignController::class, 'update'])->name('admin.vital-signs.update');
    Route::delete('/vital-signs/{vitalSign}', [VitalSignController::class, 'destroy'])->name('admin.vital-signs.destroy');
    Route::get('/vital-signs/trend/{patientId}', [VitalSignController::class, 'trend'])->name('admin.vital-signs.trend');
    Route::get('/vital-signs/trend/{patientId}/data', [VitalSignController::class, 'trendData'])->name('admin.vital-signs.trend-data');
    Route::get('/vital-signs/flipbox-trend/{patientId}', [VitalSignController::class, 'flipboxTrend'])->name('admin.vital-signs.flipbox-trend');
    Route::get('/vital-signs/iframe-trend/{patientId}', [VitalSignController::class, 'iframeTrend'])->name('admin.vital-signs.iframe-trend');
    
    // Bed Management Routes
    Route::prefix('beds')->name('admin.beds.')->group(function () {
        // Wards Routes
        Route::resource('wards', App\Http\Controllers\Admin\WardController::class)->names('wards');
        
        // Ward Dashboard
        Route::get('wards/{ward}/dashboard', [App\Http\Controllers\Admin\WardController::class, 'dashboard'])->name('wards.dashboard');
        
        // Ward Patient Alerts
        Route::get('wards/{ward}/alerts', [App\Http\Controllers\Admin\WardController::class, 'getPatientAlerts'])->name('wards.alerts');
        Route::get('wards/{ward}/alerts/history', [App\Http\Controllers\Admin\WardController::class, 'getPatientAlertsHistory'])->name('wards.alerts.history');
        Route::put('wards/alerts/{alertId}/seen', [App\Http\Controllers\Admin\WardController::class, 'markAlertAsSeen'])->name('wards.alerts.seen');
        Route::put('wards/alerts/{alertId}/respond', [App\Http\Controllers\Admin\WardController::class, 'respondToAlert'])->name('wards.alerts.respond');
        
        // Ward Dashboard - Admit Patient
        Route::get('wards/{ward}/admit/{bedId}', [App\Http\Controllers\Admin\WardController::class, 'admitPatient'])->name('wards.admit');
        Route::get('wards/{ward}/admit/{bedId}/iframe', [App\Http\Controllers\Admin\WardController::class, 'iframeAdmitPatient'])->name('wards.admit.iframe');
        Route::post('wards/{ward}/admit/{bedId}', [App\Http\Controllers\Admin\WardController::class, 'storeAdmission'])->name('wards.admit.store');
        
        // Patient Details in Ward
        Route::get('wards/{ward}/bed/{bedId}/patient', [App\Http\Controllers\Admin\WardController::class, 'patientDetails'])->name('wards.patient.details');
        Route::get('wards/{ward}/bed/{bedId}/patient/iframe', [App\Http\Controllers\Admin\WardController::class, 'iframePatientDetails'])->name('wards.patient.iframe');
        Route::post('wards/{ward}/bed/{bedId}/risk-factors', [App\Http\Controllers\Admin\WardController::class, 'updateRiskFactors'])->name('wards.patient.updateRiskFactors');
        
        // Patient Movement
        Route::post('wards/{ward}/bed/{bedId}/movement', [PatientMovementController::class, 'scheduleMovement'])->name('wards.patient.scheduleMovement');
        
        // Patient Bed Transfer
        Route::post('wards/{ward}/bed/{bedId}/transfer', [App\Http\Controllers\Admin\WardController::class, 'transferPatientBed'])->name('wards.patient.transferBed');
        Route::get('wards/{ward}/available-beds', [App\Http\Controllers\Admin\WardController::class, 'getAvailableBeds'])->name('wards.availableBeds');
        
        // Consultants List
        Route::get('wards/{ward}/consultants', [App\Http\Controllers\Admin\WardController::class, 'showConsultants'])->name('wards.consultants');
        
        // Beds Routes
        Route::resource('beds', App\Http\Controllers\Admin\BedController::class)->names('beds');
        
        // Admit Patient to Bed
        Route::get('beds/{bed}/admit', [App\Http\Controllers\Admin\BedController::class, 'admitPatient'])->name('beds.admit');
        
        // Discharge Patient from Bed
        Route::post('beds/{bed}/discharge', [App\Http\Controllers\Admin\BedController::class, 'discharge'])->name('beds.discharge');
        
        // Mark Bed Cleaning as Done
        Route::post('beds/{bed}/cleaning-done', [App\Http\Controllers\Admin\BedController::class, 'markCleaningDone'])->name('beds.cleaning-done');
    });
    
    // Analytics Dashboard - moved outside beds prefix
    Route::get('analytics-dashboard', [AnalyticsDashboardController::class, 'index'])->name('admin.analytics-dashboard');
    
    // Notification Demo Page for Ward (moved out of nested group)
    Route::get('beds/wards/{ward}/notification-demo', function($ward) {
        $ward = \App\Models\Ward::findOrFail($ward);
        
        // Get recent unresolved patient alerts for this ward
        $patientAlerts = \App\Models\PatientAlert::where('ward_id', $ward->id)
            ->whereIn('status', ['new', 'seen'])
            ->with(['patient', 'bed'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.beds.wards.notification_demo', compact('ward', 'patientAlerts'));
    })->name('admin.beds.wards.notification.demo');
    
    // Food Ordering Routes
    Route::prefix('food-orders')->name('admin.food-orders.')->group(function () {
        Route::get('/', [FoodOrderController::class, 'index'])->name('index');
        Route::get('/breakfast', [FoodOrderController::class, 'breakfastOrders'])->name('breakfast');
        Route::get('/lunch', [FoodOrderController::class, 'lunchOrders'])->name('lunch');
        Route::get('/dinner', [FoodOrderController::class, 'dinnerOrders'])->name('dinner');
        Route::get('/snacks', [FoodOrderController::class, 'snackOrders'])->name('snacks');
        Route::put('/{id}/status', [FoodOrderController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}', [FoodOrderController::class, 'destroy'])->name('destroy');
    });
    
    // Food Menu Management
    Route::resource('food-menu', FoodMenuController::class)->names('admin.food-menu');
    
    // Patient Panel Route
    Route::get('patients/{patient}/panel', [PatientPanelController::class, 'showPanel'])->name('admin.patients.panel');
    
    // Patient Alert Route
    Route::post('patients/{patient}/alert', [PatientPanelController::class, 'sendAlert'])->name('admin.patients.alert.send');
    
    // Patient Satisfaction Survey Route
    Route::post('patients/{patient}/survey', [PatientPanelController::class, 'storeSurvey'])->name('admin.patients.survey.store');
    
    // Patient Notification/Response Routes
    Route::get('patients/{patient}/responses', [PatientPanelController::class, 'getResponses'])->name('admin.patients.responses');
    Route::put('patients/responses/{responseId}/read', [PatientPanelController::class, 'markResponseAsRead'])->name('admin.patients.responses.read');
    
    // Patient notification routes for new notification system
    Route::get('patients/{patient}/notifications', [PatientController::class, 'getNotifications'])->name('admin.patients.notifications');
    Route::get('patients/{patient}/notifications/count', [PatientController::class, 'getNotificationCount'])->name('admin.patients.notifications.count');
    Route::post('patients/{patient}/notifications/{notification}/read', [PatientController::class, 'markNotificationAsRead'])->name('admin.patients.notifications.read');
    
    // PAC (Patient Admission Centre) Routes
    Route::prefix('pac')->name('admin.pac.')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Admin\PacController::class, 'dashboard'])->name('dashboard');
        Route::get('available-beds', [App\Http\Controllers\Admin\PacController::class, 'getAvailableBeds'])->name('available-beds');
        Route::get('admit/{wardId}', [App\Http\Controllers\Admin\PacController::class, 'admitPatient'])->name('admit');
        Route::post('admit', [App\Http\Controllers\Admin\PacController::class, 'storeAdmission'])->name('admit.store');
        Route::get('consultants-by-specialty', [App\Http\Controllers\Admin\PacController::class, 'getConsultantsBySpecialty'])->name('consultants-by-specialty');
    });
    
    // Room Cleaning Routes
    Route::prefix('cleaning')->name('admin.cleaning.')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Admin\CleaningController::class, 'dashboard'])->name('dashboard');
        Route::post('beds/{bedId}/mark-cleaned', [App\Http\Controllers\Admin\CleaningController::class, 'markAsCleaned'])->name('mark-cleaned');
        Route::post('beds/mark-multiple-cleaned', [App\Http\Controllers\Admin\CleaningController::class, 'markMultipleAsCleaned'])->name('mark-multiple-cleaned');
    Route::post('send-whatsapp-notification', [App\Http\Controllers\Admin\CleaningController::class, 'sendWhatsAppNotification'])->name('send-whatsapp-notification');
    });
    
    // Test route for creating alerts
    Route::post('beds/wards/{ward}/create-test-alert', function($ward) {
        $ward = \App\Models\Ward::findOrFail($ward);
        
        // Get a random bed with a patient in this ward
        $bed = $ward->beds()->where('status', 'occupied')->whereNotNull('patient_id')->inRandomOrder()->first();
        
        if (!$bed) {
            return response()->json(['success' => false, 'message' => 'No patients found in ward'], 400);
        }
        
        // Create a new test alert
        $alertTypes = ['emergency', 'pain', 'assistance', 'water', 'bathroom', 'food'];
        $alertMessages = [
            'emergency' => 'TEST: Patient experiencing chest pain!',
            'pain' => 'TEST: Patient requesting pain medication.',
            'assistance' => 'TEST: Patient needs help getting to the bathroom.',
            'water' => 'TEST: Patient requesting water.',
            'bathroom' => 'TEST: Patient needs to use the restroom urgently.',
            'food' => 'TEST: Patient requesting meal assistance.',
        ];
        
        $alertType = $alertTypes[array_rand($alertTypes)];
        
        $alert = \App\Models\PatientAlert::create([
            'patient_id' => $bed->patient_id,
            'ward_id' => $ward->id,
            'bed_id' => $bed->id,
            'alert_type' => $alertType,
            'message' => $alertMessages[$alertType],
            'status' => 'new',
            'is_urgent' => in_array($alertType, ['emergency', 'pain']),
        ]);
        
        return response()->json(['success' => true, 'alert' => $alert, 'message' => 'Test alert created']);
    })->name('admin.beds.wards.create-test-alert');
    
    // Patient Food Ordering Routes
    Route::post('patients/{patient}/food-order', [PatientPanelController::class, 'storeOrder'])->name('admin.patients.food-order.store');
    Route::delete('food-order/{orderId}/cancel', [PatientPanelController::class, 'cancelOrder'])->name('admin.food-order.cancel');
});

// Hospital Admin Routes
Route::prefix('hospital')->group(function () {
    Route::get('/dashboard', [HospitalDashboardController::class, 'index'])->name('hospital.dashboard');
});

// Consultant Routes
Route::prefix('consultant')->group(function () {
    Route::get('/dashboard', [ConsultantDashboardController::class, 'index'])->name('consultant.dashboard');
});

// GP Doctor Routes
Route::prefix('doctor')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
});

// Booking Agent Routes
Route::prefix('booking')->group(function () {
    Route::get('/dashboard', [BookingDashboardController::class, 'index'])->name('booking.dashboard');
});

// Add a direct fix for the route that seems to be causing issues - this is a troubleshooting method
Route::get('/admin/specialties/by-hospital', [App\Http\Controllers\Admin\SpecialtyController::class, 'getSpecialtiesByHospitalDirect'])->name('admin.specialties.by-hospital.direct');

// Add direct route for consultants by specialty
Route::get('/admin/referrals/consultants-by-specialty', [App\Http\Controllers\Admin\PatientReferralController::class, 'getConsultantsBySpecialtyDirect'])->name('admin.referrals.consultants-by-specialty.direct');

// Add a named route 'admin.movements.send' for sending a patient movement
Route::put('movements/{movement}/send', [App\Http\Controllers\Admin\PatientMovementController::class, 'sendPatient'])->name('admin.movements.send');

// Add a named route 'admin.movements.cancel' for canceling a patient movement
Route::put('movements/{movement}/cancel', [App\Http\Controllers\Admin\PatientMovementController::class, 'cancelMovement'])->name('admin.movements.cancel');

// Add a named route 'admin.movements.return' for returning a patient from movement
Route::put('movements/{movement}/return', [App\Http\Controllers\Admin\PatientMovementController::class, 'returnPatient'])->name('admin.movements.return');

// Test route for consultants by specialty (without authentication)
Route::get('/test/consultants-by-specialty', function(Request $request) {
    $specialtyId = $request->input('specialty_id');
    
    if (!$specialtyId) {
        return response()->json(['error' => 'Specialty ID is required'], 400);
    }
    
    $consultants = \App\Models\Consultant::where('specialty_id', $specialtyId)
        ->where('is_active', true)
        ->get(['id', 'name', 'specialty_id']);
        
    return response()->json($consultants);
})->name('test.consultants-by-specialty');
