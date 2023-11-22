<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
