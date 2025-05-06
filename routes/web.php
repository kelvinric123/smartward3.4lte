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
        
        // Patient Referral
        Route::post('wards/{ward}/bed/{bedId}/referral', [PatientReferralController::class, 'createReferral'])->name('wards.patient.createReferral');
        
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
