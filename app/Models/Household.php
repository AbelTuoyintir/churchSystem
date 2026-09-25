<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Household extends Model
{
    protected $fillable = [
        'name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(HouseholdMember::class);
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'household_members')
            ->withPivot('role')
            ->withTimestamps();
    }
}
