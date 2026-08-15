<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hospital_id', 'name', 'description', 'icon', 'status',
    ];

    protected $casts = ['status' => 'string'];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getQueueNumberForToday(): int
    {
        $today = now()->toDateString();
        $lastQueue = Appointment::where('department_id', $this->id)
            ->whereDate('appointment_date', $today)
            ->whereNotNull('queue_number')
            ->max('queue_number');
        return ($lastQueue ?? 0) + 1;
    }
}