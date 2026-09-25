<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'preferred_name',
        'email',
        'phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'marital_status',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'photo_path',
        'membership_status',
        'joined_at',
        'baptized_at',
        'email_opt_in',
        'sms_opt_in',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joined_at' => 'date',
        'baptized_at' => 'date',
        'email_opt_in' => 'boolean',
        'sms_opt_in' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function householdMemberships(): HasMany
    {
        return $this->hasMany(HouseholdMember::class);
    }

    public function households(): BelongsToMany
    {
        return $this->belongsToMany(Household::class, 'household_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    public function ledGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'leader_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function messageRecipients(): HasMany
    {
        return $this->hasMany(MessageRecipient::class);
    }

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
