<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'duration_days',
        'max_doctors', 'max_departments', 'max_patients_per_month',
        'features', 'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
    ];

    public function hospitals()
    {
        return $this->hasMany(Hospital::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}