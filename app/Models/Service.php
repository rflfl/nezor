<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'duration_minutes',
        'price',
        'professional_percentage',
        'salon_percentage',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'professional_percentage' => 'decimal:2',
        'salon_percentage' => 'decimal:2',
        'duration_minutes' => 'integer',
        'active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function dailyServiceEntries()
    {
        return $this->hasMany(DailyServiceEntry::class);
    }
}