<?php

namespace App\Providers;

use App\Contracts\MessageSender;
use App\Models\Household;
use App\Models\Message;
use App\Models\Person;
use App\Policies\HouseholdPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PersonPolicy;
use App\Services\NullMessageSender;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MessageSender::class, NullMessageSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Person::class, PersonPolicy::class);
        Gate::policy(Household::class, HouseholdPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
    }
}
