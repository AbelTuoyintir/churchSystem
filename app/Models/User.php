<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPastor(): bool
    {
        return in_array($this->role, ['pastor', 'admin']);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'pastor', 'staff']);
    }

    public function isLeader(): bool
    {
        if (in_array($this->role, ['admin', 'pastor', 'staff', 'leader'])) {
            return true;
        }

        if ($this->person_id && $this->person) {
            return $this->person->ledGroups()->exists() ||
                $this->person->groups()->wherePivot('role', 'leader')->exists();
        }

        return false;
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
