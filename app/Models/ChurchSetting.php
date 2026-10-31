<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChurchSetting extends Model
{
    protected $fillable = ['key', 'value', 'is_secret'];

    protected function casts(): array
    {
        return [
            'value' => 'encrypted',
            'is_secret' => 'boolean',
        ];
    }
}
