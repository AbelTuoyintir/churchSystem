<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'leader_id',
        'meeting_day',
        'meeting_time',
        'location',
        'is_active',
        'is_open',
    ];

    protected $casts = [
        'meeting_time' => 'datetime:H:i:s',
        'is_active' => 'boolean',
        'is_open' => 'boolean',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'leader_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'group_members')
            ->withPivot('role', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(GroupJoinRequest::class);
    }

    public function scopeLedBy(Builder $query, User $user): void
    {
        if ($user->person_id === null) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->where(function (Builder $query) use ($user): void {
            $query->where('leader_id', $user->person_id)
                ->orWhereHas('members', function (Builder $query) use ($user): void {
                    $query->where('person_id', $user->person_id)
                        ->where('role', 'leader')
                        ->whereNull('left_at');
                });
        });
    }
}
