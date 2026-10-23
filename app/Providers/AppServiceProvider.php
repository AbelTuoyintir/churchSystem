<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Contracts\MessageSender::class, \App\Services\ChurchMessageSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Person::class, \App\Policies\PersonPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Household::class, \App\Policies\HouseholdPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Group::class, \App\Policies\GroupPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Service::class, \App\Policies\ServicePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Attendance::class, \App\Policies\AttendancePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Message::class, \App\Policies\MessagePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Fund::class, \App\Policies\FundPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Contribution::class, \App\Policies\ContributionPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Pledge::class, \App\Policies\PledgePolicy::class);
    }
}
