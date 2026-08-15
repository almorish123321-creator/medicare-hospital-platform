<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHospitalAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Super admin has access to all hospitals
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Hospital-based roles must have a hospital
        if (!$user->hospital_id) {
            return response()->json([
                'message' => __('auth.no_hospital_assigned'),
            ], 403);
        }

        // Check if user's hospital is active
        if ($user->hospital && $user->hospital->status !== 'active') {
            return response()->json([
                'message' => __('auth.hospital_inactive'),
            ], 403);
        }

        return $next($request);
    }
}