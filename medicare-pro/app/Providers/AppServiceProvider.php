<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use App\Repositories\AppointmentRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\DoctorRepository;
use App\Repositories\HospitalRepository;
use App\Repositories\PatientRepository;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use App\Repositories\Interfaces\PatientRepositoryInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AppointmentRepositoryInterface::class, fn () => new AppointmentRepository(new Appointment));
        $this->app->bind(DoctorRepositoryInterface::class, fn () => new DoctorRepository(new Doctor));
        $this->app->bind(PatientRepositoryInterface::class, fn () => new PatientRepository(new Patient));
        $this->app->bind(HospitalRepositoryInterface::class, fn () => new HospitalRepository(new Hospital));
        $this->app->bind(DepartmentRepositoryInterface::class, fn () => new DepartmentRepository(new Department));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
