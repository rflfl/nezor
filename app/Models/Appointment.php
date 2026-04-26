<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'professional_id',
        'service_id',
        'appointment_date',
        'starts_at',
        'ends_at',
        'status',
        'service_price',
        'professional_percentage',
        'professional_amount',
        'salon_amount',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'starts_at' => 'datetime:H:i',
        'ends_at' => 'datetime:H:i',
        'service_price' => 'decimal:2',
        'professional_percentage' => 'decimal:2',
        'professional_amount' => 'decimal:2',
        'salon_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}