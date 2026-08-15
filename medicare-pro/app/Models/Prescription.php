<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'medical_record_id', 'doctor_id', 'patient_id',
        'diagnosis', 'instructions', 'status',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->items->sum(fn ($item) => 0); // medications have prices in medications table
    }
}