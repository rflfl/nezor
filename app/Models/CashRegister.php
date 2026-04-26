<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegister extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'opened_by',
        'closed_by',
        'open_date',
        'open_time',
        'opening_amount',
        'expected_amount',
        'counted_amount',
        'difference_amount',
        'status',
        'closing_note',
    ];

    protected $casts = [
        'opening_amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'counted_amount' => 'decimal:2',
        'difference_amount' => 'decimal:2',
        'open_date' => 'date',
        'open_time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function dailyServiceEntries()
    {
        return $this->hasMany(DailyServiceEntry::class);
    }
}