<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamPosition extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'description',
        'slots_per_service',
    ];

    protected $casts = [
        'slots_per_service' => 'integer',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'position_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'position_id');
    }
}
