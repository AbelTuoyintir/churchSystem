<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            $user->ensureDefaultRolesExist();
        });

        static::saved(function (self $user) {
            $user->ensureDefaultRolesExist();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'person_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function authoredMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'created_by');
    }

    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    public function recordedContributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'recorded_by');
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    protected function ensureDefaultRolesExist(): void
    {
        foreach (['admin', 'pastor', 'steward', 'member', 'leader', 'staff', 'viewer'] as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->role === 'admin';
    }

    public function isPastor(): bool
    {
        return $this->hasRole(['pastor', 'admin']) || in_array($this->role, ['pastor', 'admin']);
    }

    public function isStaff(): bool
    {
        return $this->hasRole(['admin', 'pastor', 'steward', 'staff']) || in_array($this->role, ['admin', 'pastor', 'steward', 'staff']);
    }

    public function isLeader(): bool
    {
        if ($this->hasRole(['admin', 'pastor', 'steward', 'leader', 'staff']) || in_array($this->role, ['admin', 'pastor', 'steward', 'leader', 'staff'])) {
            return true;
        }

        if ($this->person_id && $this->person) {
            return $this->person->ledGroups()->exists() ||
                $this->person->groups()->wherePivot('role', 'leader')->exists();
        }

        return false;
    }

    public function isGroupLeader(): bool
    {
        return $this->person_id !== null && Group::query()->ledBy($this)->exists();
    }

    public function isMember(): bool
    {
        return $this->hasRole('member') || $this->role === 'member';
    }

    public function syncLegacyRoleFromRoles(): void
    {
        $roleNames = $this->getRoleNames()->all();

        if (empty($roleNames)) {
            if ($this->role === null || $this->role === '') {
                $this->role = 'member';
            }

            return;
        }

        $preferred = collect($roleNames)->first(fn (string $roleName): bool => in_array($roleName, ['admin', 'pastor', 'steward', 'member'], true));

        $this->role = $preferred ?? $roleNames[0];

        if ($this->exists) {
            $this->saveQuietly();
        }
    }
}
