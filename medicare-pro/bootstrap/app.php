<?php

use App\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Delegate scheduling to our custom Console Kernel
        $kernel = app(ConsoleKernel::class);
        $kernel->schedule($schedule);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'doctor' => \App\Http\Middleware\DoctorMiddleware::class,
            'admin_doctor' => \App\Http\Middleware\AdminDoctorMiddleware::class,
            'admin_nurse' => \App\Http\Middleware\AdminNurseMiddleware::class,
            'admin_doctor_nurse' => \App\Http\Middleware\AdminDoctorNurseMiddleware::class,
            'nurse' => \App\Http\Middleware\NurseMiddleware::class,
            'admin_nurse_pharmacist' => \App\Http\Middleware\AdminNursePharmacistMiddleware::class,
            'pharmacist' => \App\Http\Middleware\PharmacistMiddleware::class,
            'patient' => \App\Http\Middleware\PatientMiddleware::class,

            'role' => \App\Http\Middleware\CheckRole::class,
            'hospital_access' => \App\Http\Middleware\CheckHospitalAccess::class,
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
