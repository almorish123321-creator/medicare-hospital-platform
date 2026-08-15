<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id', 'appointment_id', 'hospital_id',
        'amount', 'discount', 'tax', 'total_amount',
        'status', 'payment_method', 'insurance_provider',
        'insurance_coverage', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'insurance_coverage' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'pending');
    }

    public function getPaidAmountAttribute(): float
    {
        return $this->payments()->where('status', 'success')->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->getPaidAmountAttribute());
    }
}