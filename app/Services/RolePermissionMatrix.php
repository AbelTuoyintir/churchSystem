<?php

namespace App\Services;

use App\Models\ChurchSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionMatrix
{
    public const MODULES = [
        'people' => ['label' => 'Directory', 'model' => \App\Models\Person::class],
        'households' => ['label' => 'Households', 'model' => \App\Models\Household::class],
        'groups' => ['label' => 'Groups', 'model' => \App\Models\Group::class],
        'services' => ['label' => 'Services', 'model' => \App\Models\Service::class],
        'attendance' => ['label' => 'Attendance', 'model' => \App\Models\Attendance::class],
        'messages' => ['label' => 'Messages', 'model' => \App\Models\Message::class],
        'events' => ['label' => 'Events', 'model' => \App\Models\Event::class],
        'teams' => ['label' => 'Teams', 'model' => \App\Models\Team::class],
        'assignments' => ['label' => 'Scheduling', 'model' => \App\Models\Assignment::class],
        'funds' => ['label' => 'Funds', 'model' => \App\Models\Fund::class],
        'contributions' => ['label' => 'Contributions', 'model' => \App\Models\Contribution::class],
        'pledges' => ['label' => 'Pledges', 'model' => \App\Models\Pledge::class],
        'pastoral_care' => ['label' => 'Pastoral Care', 'model' => \App\Models\PastoralCareNote::class],
    ];

    public const ACTIONS = ['view', 'create', 'update', 'delete'];

    public function initializeDefaults(): void
    {
        if (ChurchSetting::query()->where('key', 'role_permission_matrix_initialized')->exists()) {
            return;
        }

        DB::transaction(function (): void {
            $roles = collect(['admin', 'pastor', 'steward', 'member', 'leader', 'staff', 'viewer'])
                ->mapWithKeys(fn (string $name): array => [$name => Role::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'web',
                ])]);

            $permissions = collect(self::permissionNames())
                ->mapWithKeys(fn (string $name): array => [$name => Permission::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'web',
                ])]);

            foreach ($roles as $roleName => $role) {
                $allowedModules = match ($roleName) {
                    'admin', 'pastor' => array_keys(self::MODULES),
                    'staff' => array_values(array_diff(array_keys(self::MODULES), ['pastoral_care'])),
                    'steward' => ['teams', 'assignments'],
                    'leader' => ['people', 'households', 'groups', 'services', 'attendance', 'messages', 'events', 'teams', 'assignments'],
                    'viewer' => ['people', 'households', 'groups', 'services', 'attendance', 'messages', 'events', 'teams'],
                    'member' => ['people', 'households', 'groups', 'events'],
                    default => [],
                };

                $allowedActions = match ($roleName) {
                    'admin', 'pastor', 'staff' => self::ACTIONS,
                    'steward' => ['view', 'create', 'update', 'delete'],
                    'leader' => ['view', 'create', 'update', 'delete'],
                    'member' => ['view'],
                    default => ['view'],
                };

                $permissionsForRole = collect($allowedModules)
                    ->flatMap(fn (string $module) => collect($allowedActions)
                        ->map(fn (string $action): Permission => $permissions["{$module}.{$action}"]))
                    ->all();

                if ($roleName === 'member') {
                    $permissionsForRole[] = $permissions['people.update'];
                }

                $role->syncPermissions($permissionsForRole);
            }

            ChurchSetting::query()->create([
                'key' => 'role_permission_matrix_initialized',
                'value' => '1',
            ]);
        });
    }

    public function permissionFor(string $ability, array $arguments): ?string
    {
        $resource = $arguments[0] ?? null;
        $model = is_object($resource) ? $resource::class : $resource;
        $module = collect(self::MODULES)->search(fn (array $definition): bool => $definition['model'] === $model);

        if ($module === false) {
            return null;
        }

        $action = match ($ability) {
            'viewAny', 'view' => 'view',
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete',
            default => null,
        };

        return $action === null ? null : "{$module}.{$action}";
    }

    public function authorize(User $user, string $ability, array $arguments): ?bool
    {
        if ($user->isAdmin()) {
            return null;
        }

        if (! ChurchSetting::query()->where('key', 'role_permission_matrix_initialized')->exists()) {
            return null;
        }

        $permission = $this->permissionFor($ability, $arguments);

        if ($permission === null) {
            return null;
        }

        if ($user->hasPermissionTo($permission)) {
            return null;
        }

        $roleNames = $user->getRoleNames()->push($user->role)->filter()->unique();
        $roleCanPerform = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $roleNames)
            ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
            ->exists();

        return $roleCanPerform ? null : false;
    }

    public static function permissionNames(): array
    {
        return collect(array_keys(self::MODULES))
            ->flatMap(fn (string $module) => collect(self::ACTIONS)
                ->map(fn (string $action): string => "{$module}.{$action}"))
            ->all();
    }
}