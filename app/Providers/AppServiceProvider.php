<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Amenities;
use App\Models\Reservation;
use App\Models\ReservedAmenity;
use App\Models\Bill;
use App\Models\DownPayment;
use App\Observers\AuditObserver;

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
        // Register audit observers for key models
        Admin::observe(AuditObserver::class);
        Customer::observe(AuditObserver::class);
        Amenities::observe(AuditObserver::class);
        Reservation::observe(AuditObserver::class);
        ReservedAmenity::observe(AuditObserver::class);
        Bill::observe(AuditObserver::class);
        DownPayment::observe(AuditObserver::class);
    }
}
