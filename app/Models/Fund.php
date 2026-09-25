<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fund extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_tax_deductible',
        'goal_amount',
        'is_active',
    ];

    protected $casts = [
        'is_tax_deductible' => 'boolean',
        'goal_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }
}
