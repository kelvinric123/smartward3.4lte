<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Hospital\DashboardController as HospitalDashboardController;
use App\Http\Controllers\Consultant\DashboardController as ConsultantDashboardController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Booking\DashboardController as BookingDashboardController;
use App\Http\Controllers\WardAdmin\DashboardController as WardAdminDashboardController;
use App\Http\Controllers\WardAdmin\LoginController as WardAdminLoginController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\ConsultantController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\VitalSignController;
use App\Http\Controllers\Admin\PatientDischargeController;
use App\Http\Controllers\Admin\PatientMovementController;
use App\Http\Controllers\Admin\PatientReferralController;

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
    if (Auth::check() && Auth::user()->hasRole('ward-admin')) {
        return redirect()->route('admin.beds.wards.dashboard.direct', ['ward' => 1]);
    }
    return redirect()->route('wardadmin.login');
});

// Authentication Routes
Auth::routes();

// Ward Admin Routes
Route::prefix('wardadmin')->group(function () {
    // Root path redirect to login if not authenticated or dashboard if authenticated
    Route::get('/', function () {
        if (Auth::check() && Auth::user()->hasRole('ward-admin')) {
            return redirect()->route('admin.beds.wards.dashboard.direct', ['ward' => 1]);
        }
        return redirect()->route('wardadmin.login');
    });
    
    Route::get('login', [WardAdminLoginController::class, 'showLoginForm'])->name('wardadmin.login');
    Route::post('login', [WardAdminLoginController::class, 'login']);
    
    // Fullscreen dashboard - uses existing ward dashboard view with the fullscreen middleware
    Route::get('dashboard/{wardId}', [WardAdminDashboardController::class, 'dashboard'])
        ->name('wardadmin.dashboard')
        ->middleware(['auth', 'role:ward-admin,super-admin', 'wardadmin.fullscreen']);
});

// Default home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('hospitals', App\Http\Controllers\Admin\HospitalController::class)->names('admin.hospitals');
    Route::resource('specialties', SpecialtyController::class)->names('admin.specialties');
    Route::resource('consultants', ConsultantController::class)->names('admin.consultants');
    Route::resource('nurses', App\Http\Controllers\Admin\NurseController::class)->names('admin.nurses');
    Route::resource('patients', PatientController::class)->names('admin.patients');
    
    // Patient Discharge Routes
    Route::get('patients/{patientId}/discharge', [PatientDischargeController::class, 'create'])->name('admin.patients.discharge');
    Route::post('patients/{patientId}/discharge', [PatientDischargeController::class, 'store'])->name('admin.patients.discharge.store');
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
    Route::resource('vital-signs', VitalSignController::class)->names('admin.vital-signs');
    Route::get('vital-signs/patient/{patientId}/trend', [VitalSignController::class, 'trend'])->name('admin.vital-signs.trend');
    Route::get('vital-signs/patient/{patientId}/flipbox-trend', [VitalSignController::class, 'flipboxTrend'])->name('admin.vital-signs.flipbox-trend');
    
    // Bed Management Routes
    Route::prefix('beds')->name('admin.beds.')->group(function () {
        // Wards Routes
        Route::resource('wards', App\Http\Controllers\Admin\WardController::class)->names('wards');
        
        // Ward Dashboard
        Route::get('wards/{ward}/dashboard', [App\Http\Controllers\Admin\WardController::class, 'dashboard'])->name('wards.dashboard');
        
        // Ward Dashboard - Admit Patient
        Route::get('wards/{ward}/admit/{bedId}', [App\Http\Controllers\Admin\WardController::class, 'admitPatient'])->name('wards.admit');
        Route::post('wards/{ward}/admit/{bedId}', [App\Http\Controllers\Admin\WardController::class, 'storeAdmission'])->name('wards.admit.store');
        
        // Patient Details in Ward
        Route::get('wards/{ward}/bed/{bedId}/patient', [App\Http\Controllers\Admin\WardController::class, 'patientDetails'])->name('wards.patient.details');
        Route::post('wards/{ward}/bed/{bedId}/risk-factors', [App\Http\Controllers\Admin\WardController::class, 'updateRiskFactors'])->name('wards.patient.updateRiskFactors');
        
        // Patient Movement
        Route::post('wards/{ward}/bed/{bedId}/movement', [PatientMovementController::class, 'scheduleMovement'])->name('wards.patient.scheduleMovement');
        
        // Beds Routes
        Route::resource('beds', App\Http\Controllers\Admin\BedController::class)->names('beds');
        
        // Admit Patient to Bed
        Route::get('beds/{bed}/admit', [App\Http\Controllers\Admin\BedController::class, 'admitPatient'])->name('beds.admit');
        
        // Discharge Patient from Bed
        Route::post('beds/{bed}/discharge', [App\Http\Controllers\Admin\BedController::class, 'discharge'])->name('beds.discharge');
    });
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

// Add direct routes for ward admins to access various ward features
Route::get('/admin/beds/wards/{ward}/dashboard', [\App\Http\Controllers\WardAdmin\DashboardController::class, 'dashboard'])
    ->name('admin.beds.wards.dashboard.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin', 'wardadmin.fullscreen']);

// Ward Admin - Patient Admission Routes
Route::get('/admin/beds/wards/{ward}/admit/{bedId}', [\App\Http\Controllers\WardAdmin\DashboardController::class, 'admitPatient'])
    ->name('admin.beds.wards.admit.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin', 'wardadmin.fullscreen']);

Route::post('/admin/beds/wards/{ward}/admit/{bedId}', [\App\Http\Controllers\WardAdmin\DashboardController::class, 'storeAdmission'])
    ->name('admin.beds.wards.admit.store.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);

// Ward Admin - Patient Details Routes
Route::get('/admin/beds/wards/{ward}/bed/{bedId}/patient', [\App\Http\Controllers\WardAdmin\DashboardController::class, 'patientDetails'])
    ->name('admin.beds.wards.patient.details.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin', 'wardadmin.fullscreen']);

Route::post('/admin/beds/wards/{ward}/bed/{bedId}/risk-factors', [\App\Http\Controllers\WardAdmin\DashboardController::class, 'updateRiskFactors'])
    ->name('admin.beds.wards.patient.updateRiskFactors.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);

// Ward Admin - Patient Movement Route
Route::post('/admin/beds/wards/{ward}/bed/{bedId}/movement', [\App\Http\Controllers\Admin\PatientMovementController::class, 'scheduleMovement'])
    ->name('admin.beds.wards.patient.scheduleMovement.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);

// Ward Admin - Patient Movement Action Routes
Route::prefix('wardadmin/movements')->name('wardadmin.movements.')->group(function () {
    Route::put('{movement}/send', [\App\Http\Controllers\Admin\PatientMovementController::class, 'sendPatient'])
        ->name('send')
        ->middleware(['auth', 'role:ward-admin,super-admin']);
    Route::put('{movement}/return', [\App\Http\Controllers\Admin\PatientMovementController::class, 'returnPatient'])
        ->name('return')
        ->middleware(['auth', 'role:ward-admin,super-admin']);
    Route::put('{movement}/cancel', [\App\Http\Controllers\Admin\PatientMovementController::class, 'cancelMovement'])
        ->name('cancel')
        ->middleware(['auth', 'role:ward-admin,super-admin']);
});

// Direct versions of the ward admin movement routes
Route::put('wardadmin/movements/{movement}/send/direct', [\App\Http\Controllers\Admin\PatientMovementController::class, 'sendPatient'])
    ->name('wardadmin.movements.send.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);
Route::put('wardadmin/movements/{movement}/return/direct', [\App\Http\Controllers\Admin\PatientMovementController::class, 'returnPatient'])
    ->name('wardadmin.movements.return.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);
Route::put('wardadmin/movements/{movement}/cancel/direct', [\App\Http\Controllers\Admin\PatientMovementController::class, 'cancelMovement'])
    ->name('wardadmin.movements.cancel.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);

// Direct routes for patient referral and discharge that can be accessed by ward admin
Route::post('/admin/patients/{patientId}/referral/direct', [\App\Http\Controllers\Admin\PatientReferralController::class, 'store'])
    ->name('admin.patients.referral.store.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);

Route::post('/admin/patients/{patientId}/discharge/direct', [\App\Http\Controllers\Admin\PatientDischargeController::class, 'store'])
    ->name('admin.patients.discharge.store.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);

// Ward Admin - Vital Signs Routes (Fullscreen Mode)
Route::get('/admin/vital-signs/patient/{patientId}/trend/direct', [\App\Http\Controllers\Admin\VitalSignController::class, 'trendWardAdmin'])
    ->name('admin.vital-signs.trend.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin', 'wardadmin.fullscreen']);

Route::get('/admin/vital-signs/patient/{patientId}/flipbox-trend/direct', [\App\Http\Controllers\Admin\VitalSignController::class, 'flipboxTrendWardAdmin'])
    ->name('admin.vital-signs.flipbox-trend.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin', 'wardadmin.fullscreen']);

Route::get('/admin/vital-signs/create/direct', [\App\Http\Controllers\Admin\VitalSignController::class, 'createWardAdmin'])
    ->name('admin.vital-signs.create.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin', 'wardadmin.fullscreen']);

Route::post('/admin/vital-signs/store/direct', [\App\Http\Controllers\Admin\VitalSignController::class, 'storeWardAdmin'])
    ->name('admin.vital-signs.store.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin']);

Route::get('/admin/vital-signs/{id}/direct', [\App\Http\Controllers\Admin\VitalSignController::class, 'show'])
    ->name('admin.vital-signs.show.direct')
    ->middleware(['auth', 'role:ward-admin,super-admin', 'wardadmin.fullscreen']);
