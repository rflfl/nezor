<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\DailyServiceEntry;
use App\Models\Professional;
use App\Models\Service;
use App\Policies\AppointmentPolicy;
use App\Policies\CashRegisterPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DailyServiceEntryPolicy;
use App\Policies\ProfessionalPolicy;
use App\Policies\ServicePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(Professional::class, ProfessionalPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(CashRegister::class, CashRegisterPolicy::class);
        Gate::policy(DailyServiceEntry::class, DailyServiceEntryPolicy::class);
    }
}
