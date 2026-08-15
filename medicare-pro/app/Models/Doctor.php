<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'department_id', 'specialty', 'qualification',
        'experience_years', 'consultation_fee', 'rating', 'total_reviews',
        'bio', 'is_available', 'schedule_settings',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
        'rating' => 'decimal:1',
        'total_reviews' => 'integer',
        'is_available' => 'boolean',
        'schedule_settings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function updateRating(int $newRating): void
    {
        $total = $this->total_reviews;
        $currentAvg = $this->rating;
        $newTotal = $total + 1;
        $newAvg = (($currentAvg * $total) + $newRating) / $newTotal;
        $this->update([
            'rating' => round($newAvg, 1),
            'total_reviews' => $newTotal,
        ]);
    }

    public function hospital()
    {
        return $this->department->hospital;
    }
}
