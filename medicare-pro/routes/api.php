<?php

use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Api\Admin\DepartmentController;
use App\Http\Controllers\Api\Admin\DoctorController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Doctor\AppointmentController as DoctorAppointment;
use App\Http\Controllers\Api\Doctor\DashboardController as DoctorDashboard;
use App\Http\Controllers\Api\Doctor\MedicalRecordController as DoctorMedicalRecord;
use App\Http\Controllers\Api\Doctor\PrescriptionController as DoctorPrescription;
use App\Http\Controllers\Api\Doctor\ScheduleController;
use App\Http\Controllers\Api\Nurse\VitalSignController;
use App\Http\Controllers\Api\Patient\AppointmentController as PatientAppointment;
use App\Http\Controllers\Api\Patient\InvoiceController;
use App\Http\Controllers\Api\Patient\MedicalRecordController as PatientMedicalRecord;
use App\Http\Controllers\Api\Patient\NotificationController;
use App\Http\Controllers\Api\Patient\PrescriptionController as PatientPrescription;
use App\Http\Controllers\Api\Patient\ProfileController;
use App\Http\Controllers\Api\Patient\QueueController as PatientQueue;
use App\Http\Controllers\Api\Pharmacist\MedicationController;
use App\Http\Controllers\Api\Pharmacist\PrescriptionController as PharmacistPrescription;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\Receptionist\AppointmentController as ReceptionistAppointment;
use App\Http\Controllers\Api\Receptionist\DashboardController as ReceptionistDashboard;
use App\Http\Controllers\Api\Receptionist\PatientController as ReceptionistPatient;
use App\Http\Controllers\Api\Receptionist\QueueController as ReceptionistQueue;
use App\Http\Controllers\Api\SuperAdmin\AnalyticsController;
use App\Http\Controllers\Api\SuperAdmin\HospitalController;
use App\Http\Controllers\Api\SuperAdmin\LanguageController as SuperAdminLanguage;
use App\Http\Controllers\Api\SuperAdmin\PlanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════════════════════════════════
// PUBLIC ROUTES (No Authentication Required)
// ═══════════════════════════════════════════════════════════════════
Route::prefix('v1')->group(function () {
    // Public endpoints
    Route::get('/hospitals', [PublicController::class, 'hospitals']);
    Route::get('/hospitals/{hospital}', [PublicController::class, 'hospital']);
    Route::get('/hospitals/{hospital}/doctors', [PublicController::class, 'hospitalDoctors']);
    Route::get('/hospitals/{hospital}/departments', [PublicController::class, 'hospitalDepartments']);
    Route::get('/doctors/{doctor}', [PublicController::class, 'doctor']);
    Route::get('/doctors/{doctor}/reviews', [PublicController::class, 'doctorReviews']);
    Route::get('/doctors/{doctor}/schedule', [PublicController::class, 'doctorSchedule']);
    Route::get('/languages', [PublicController::class, 'languages']);

    // ═══════════════════════════════════════════════════════════════
    // AUTHENTICATION ROUTES
    // ═══════════════════════════════════════════════════════════════
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // ═══════════════════════════════════════════════════════════════
    // AUTHENTICATED ROUTES
    // ═══════════════════════════════════════════════════════════════
    Route::middleware(['auth:sanctum', 'set.locale'])->group(function () {

        // Auth management
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refreshToken']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/change-language', [AuthController::class, 'changeLanguage']);
        });

        // ═══════════════════════════════════════════════════════════════
        // PATIENT ROUTES
        // ═══════════════════════════════════════════════════════════════
        Route::middleware('role:patient,hospital_access')->prefix('patient')->group(function () {
            Route::get('/profile', [ProfileController::class, 'show']);
            Route::put('/profile', [ProfileController::class, 'update']);
            Route::get('/appointments', [PatientAppointment::class, 'index']);
            Route::post('/appointments', [PatientAppointment::class, 'store']);
            Route::get('/appointments/{appointment}', [PatientAppointment::class, 'show']);
            Route::delete('/appointments/{appointment}', [PatientAppointment::class, 'cancel']);
            Route::get('/queue-status', [PatientQueue::class, 'status']);
            Route::get('/medical-records', [PatientMedicalRecord::class, 'index']);
            Route::get('/medical-records/{medicalRecord}', [PatientMedicalRecord::class, 'show']);
            Route::get('/prescriptions', [PatientPrescription::class, 'index']);
            Route::get('/prescriptions/{prescription}', [PatientPrescription::class, 'show']);
            Route::get('/invoices', [InvoiceController::class, 'index']);
            Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'pay']);
            Route::get('/notifications', [NotificationController::class, 'index']);
            Route::put('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        });

        // ═══════════════════════════════════════════════════════════════
        // DOCTOR ROUTES
        // ═══════════════════════════════════════════════════════════════
        Route::middleware('role:doctor,hospital_access')->prefix('doctor')->group(function () {
            Route::get('/dashboard', [DoctorDashboard::class, 'index']);
            Route::get('/appointments', [DoctorAppointment::class, 'index']);
            Route::get('/appointments/{appointment}', [DoctorAppointment::class, 'show']);
            Route::put('/appointments/{appointment}/start', [DoctorAppointment::class, 'start']);
            Route::put('/appointments/{appointment}/complete', [DoctorAppointment::class, 'complete']);
            Route::post('/medical-records', [DoctorMedicalRecord::class, 'store']);
            Route::put('/medical-records/{medicalRecord}', [DoctorMedicalRecord::class, 'update']);
            Route::get('/prescriptions', [DoctorPrescription::class, 'index']);
            Route::post('/prescriptions', [DoctorPrescription::class, 'store']);
            Route::get('/patients', [ScheduleController::class, 'patients']);
            Route::get('/schedule', [ScheduleController::class, 'show']);
            Route::put('/schedule', [ScheduleController::class, 'update']);
            Route::get('/reviews', fn () => \App\Models\Review::where('doctor_id', request()->user()->doctor->id)->with('patient.user')->latest()->paginate(15));
        });

        // ═══════════════════════════════════════════════════════════════
        // RECEPTIONIST ROUTES
        // ═══════════════════════════════════════════════════════════════
        Route::middleware('role:receptionist,hospital_access')->prefix('receptionist')->group(function () {
            Route::get('/dashboard', [ReceptionistDashboard::class, 'index']);
            Route::get('/appointments', [ReceptionistAppointment::class, 'index']);
            Route::put('/appointments/{appointment}/check-in', [ReceptionistAppointment::class, 'checkIn']);
            Route::put('/appointments/{appointment}/no-show', [ReceptionistAppointment::class, 'noShow']);
            Route::post('/walk-in', [ReceptionistAppointment::class, 'walkIn']);
            Route::get('/queue', [ReceptionistQueue::class, 'index']);
            Route::post('/queue/{id}/call', [ReceptionistQueue::class, 'callNext']);
            Route::get('/patients', [ReceptionistPatient::class, 'index']);
            Route::post('/patients', [ReceptionistPatient::class, 'store']);
        });

        // ═══════════════════════════════════════════════════════════════
        // NURSE ROUTES
        // ═══════════════════════════════════════════════════════════════
        Route::middleware('role:nurse,hospital_access')->prefix('nurse')->group(function () {
            Route::get('/appointments', [VitalSignController::class, 'appointments']);
            Route::post('/vital-signs/{appointment}', [VitalSignController::class, 'storeVitalSigns']);
            Route::get('/patients/{patient}', [VitalSignController::class, 'showPatient']);
        });

        // ═══════════════════════════════════════════════════════════════
        // PHARMACIST ROUTES
        // ═══════════════════════════════════════════════════════════════
        Route::middleware('role:pharmacist,hospital_access')->prefix('pharmacist')->group(function () {
            Route::get('/prescriptions', [PharmacistPrescription::class, 'index']);
            Route::get('/prescriptions/{prescription}', [PharmacistPrescription::class, 'show']);
            Route::put('/prescriptions/{prescription}/dispense', [PharmacistPrescription::class, 'dispense']);
            Route::get('/medications', [MedicationController::class, 'index']);
            Route::post('/medications', [MedicationController::class, 'store']);
            Route::put('/medications/{medication}', [MedicationController::class, 'update']);
            Route::get('/inventory', [MedicationController::class, 'inventory']);
        });

        // ═══════════════════════════════════════════════════════════════
        // HOSPITAL ADMIN ROUTES
        // ═══════════════════════════════════════════════════════════════
        Route::middleware('role:hospital_admin,hospital_access')->prefix('admin')->group(function () {
            Route::get('/dashboard', [AdminDashboard::class, 'index']);
            Route::get('/doctors', [DoctorController::class, 'index']);
            Route::post('/doctors', [DoctorController::class, 'store']);
            Route::put('/doctors/{doctor}', [DoctorController::class, 'update']);
            Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy']);
            Route::get('/departments', [DepartmentController::class, 'index']);
            Route::post('/departments', [DepartmentController::class, 'store']);
            Route::put('/departments/{department}', [DepartmentController::class, 'update']);
            Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);
            Route::get('/receptionists', [StaffController::class, 'receptionists']);
            Route::post('/receptionists', [StaffController::class, 'addReceptionist']);
            Route::get('/nurses', [StaffController::class, 'nurses']);
            Route::post('/nurses', [StaffController::class, 'addNurse']);
            Route::get('/pharmacists', [StaffController::class, 'pharmacists']);
            Route::post('/pharmacists', [StaffController::class, 'addPharmacist']);
            Route::get('/reports', [ReportController::class, 'index']);
            Route::get('/analytics', [ReportController::class, 'doctorPerformance']);
            Route::get('/settings', [SettingController::class, 'index']);
            Route::put('/settings', [SettingController::class, 'update']);
        });

        // ═══════════════════════════════════════════════════════════════
        // SUPER ADMIN ROUTES
        // ═══════════════════════════════════════════════════════════════
        Route::middleware('role:super_admin')->prefix('super-admin')->group(function () {
            Route::get('/hospitals', [HospitalController::class, 'index']);
            Route::post('/hospitals', [HospitalController::class, 'store']);
            Route::put('/hospitals/{hospital}', [HospitalController::class, 'update']);
            Route::delete('/hospitals/{hospital}', [HospitalController::class, 'destroy']);
            Route::get('/plans', [PlanController::class, 'index']);
            Route::post('/plans', [PlanController::class, 'store']);
            Route::put('/plans/{plan}', [PlanController::class, 'update']);
            Route::delete('/plans/{plan}', [PlanController::class, 'destroy']);
            Route::get('/analytics', [AnalyticsController::class, 'index']);
            Route::get('/languages', [SuperAdminLanguage::class, 'index']);
            Route::get('/translations', [SuperAdminLanguage::class, 'translations']);
            Route::post('/translations', [SuperAdminLanguage::class, 'store']);
            Route::put('/translations/{translation}', [SuperAdminLanguage::class, 'update']);
            Route::put('/default-language', [SuperAdminLanguage::class, 'setDefaultLanguage']);
        });
    });
});
