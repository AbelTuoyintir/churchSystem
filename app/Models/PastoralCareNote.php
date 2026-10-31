<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PastoralCareNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pastoral_care_notes';

    protected $fillable = [
        'person_id',
        'author_id',
        'type',
        'title',
        'body',
        'status',
        'priority',
        'due_date',
        'visited_at',
        'location',
        'is_confidential',
    ];

    protected $casts = [
        'due_date' => 'date',
        'visited_at' => 'datetime',
        'is_confidential' => 'boolean',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

