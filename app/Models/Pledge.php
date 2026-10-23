<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pledge extends Model
{
    protected $fillable = [
        'person_id',
        'fund_id',
        'amount',
        'frequency',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function getFulfilledAmountAttribute(): float
    {
        return (float) $this->contributions()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - $this->fulfilled_amount);
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->amount <= 0) {
            return 100;
        }
        return min(100, (int) round(($this->fulfilled_amount / $this->amount) * 100));
    }
}
