<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyServiceEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'professional_id',
        'service_id',
        'cash_register_id',
        'appointment_id',
        'service_date',
        'gross_amount',
        'professional_percentage',
        'professional_amount',
        'salon_amount',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'service_date' => 'date',
        'gross_amount' => 'decimal:2',
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

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}