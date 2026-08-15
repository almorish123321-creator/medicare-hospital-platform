<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hospital = $request->user()->hospital;
        return response()->json([
            'data' => [
                'name' => $hospital->name,
                'email' => $hospital->email,
                'phone' => $hospital->phone,
                'address' => $hospital->address,
                'logo' => $hospital->logo ? asset("storage/{$hospital->logo}") : null,
                'default_language' => $hospital->default_language,
                'latitude' => (float) $hospital->latitude,
                'longitude' => (float) $hospital->longitude,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255',
            'phone' => 'sometimes|string|max:50',
            'address' => 'sometimes|string',
            'logo' => 'sometimes|image|max:2048',
            'default_language' => 'sometimes|in:ar,en',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
        ]);

        if (isset($validated['logo'])) {
            $validated['logo'] = $request->file('logo')->store('hospitals', 'public');
        }

        $request->user()->hospital->update($validated);

        return response()->json([
            'message' => __('messages.updated', ['model' => __('common.settings')]),
            'data' => $request->user()->hospital->fresh(),
        ]);
    }
}
