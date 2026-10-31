<?php

namespace App\Providers;

use App\Contracts\MessageSender;
use App\Models\Attendance;
use App\Models\Assignment;
use App\Models\Contribution;
use App\Models\Event;
use App\Models\Fund;
use App\Models\Group;
use App\Models\Household;
use App\Models\Message;
use App\Models\Person;
use App\Models\PastoralCareNote;
use App\Models\Pledge;
use App\Models\Team;
use App\Policies\AttendancePolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\ContributionPolicy;
use App\Policies\EventPolicy;
use App\Policies\FundPolicy;
use App\Policies\GroupPolicy;
use App\Policies\HouseholdPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PersonPolicy;
use App\Policies\PastoralCarePolicy;
use App\Policies\PledgePolicy;
use App\Policies\TeamPolicy;
use App\Services\NullMessageSender;
use App\Services\AuditLogger;
use App\Services\RolePermissionMatrix;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
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
        Gate::after(function (\App\Models\User $user, string $ability, ?bool $result, array $arguments): ?bool {
            if ($result !== true) {
                return null;
            }

            return app(RolePermissionMatrix::class)->authorize($user, $ability, $arguments) === false
                ? false
                : null;
        });

        DB::listen(function (QueryExecuted $query): void {
            try {
                app(AuditLogger::class)->recordQuery($query);
            } catch (\Throwable $e) {
                report($e);
            }
        });

        Gate::policy(Person::class, PersonPolicy::class);
        Gate::policy(Group::class, GroupPolicy::class);
        Gate::policy(Household::class, HouseholdPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Fund::class, FundPolicy::class);
        Gate::policy(Contribution::class, ContributionPolicy::class);
        Gate::policy(Pledge::class, PledgePolicy::class);
        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(PastoralCareNote::class, PastoralCarePolicy::class);
    }
}
