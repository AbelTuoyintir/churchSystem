<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'leader_id',
        'meeting_day',
        'meeting_time',
        'location',
        'is_active',
    ];

    protected $casts = [
        'meeting_time' => 'datetime:H:i:s',
        'is_active' => 'boolean',
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
}
