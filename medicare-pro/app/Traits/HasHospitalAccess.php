<?php

namespace App\Traits;

trait HasHospitalAccess
{
    public function scopeForHospital($query, ?int $hospitalId = null)
    {
        $hospitalId = $hospitalId ?? auth()->user()->hospital_id;
        if ($hospitalId) {
            return $query->where('hospital_id', $hospitalId);
        }
        return $query;
    }

    public function belongsToUserHospital($model): bool
    {
        $user = auth()->user();
        if ($user->isSuperAdmin()) return true;
        return $model->hospital_id === $user->hospital_id;
    }

    protected function getHospitalId(): ?int
    {
        return auth()->user()->hospital_id;
    }
}
