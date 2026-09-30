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
        $this->app->bind(\App\Contracts\MessageSender::class, \App\Services\NullMessageSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Person::class, \App\Policies\PersonPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Household::class, \App\Policies\HouseholdPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Message::class, \App\Policies\MessagePolicy::class);
    }
}
