<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $departments = Department::where('hospital_id', $request->user()->hospital_id)
            ->withCount('doctors')
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => DepartmentResource::collection($departments),
            'meta' => [
                'current_page' => $departments->currentPage(),
                'last_page' => $departments->lastPage(),
                'per_page' => $departments->perPage(),
                'total' => $departments->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        $department = Department::create([
            ...$validated,
            'hospital_id' => $request->user()->hospital_id,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => __('messages.created', ['model' => __('common.department')]),
            'data' => new DepartmentResource($department),
        ], 201);
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        if ($department->hospital_id !== $request->user()->hospital_id) {
            abort(403, __('messages.forbidden'));
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'icon' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $department->update($validated);

        return response()->json([
            'message' => __('messages.updated', ['model' => __('common.department')]),
            'data' => new DepartmentResource($department->fresh()),
        ]);
    }

    public function destroy(Request $request, Department $department): JsonResponse
    {
        if ($department->hospital_id !== $request->user()->hospital_id) {
            abort(403, __('messages.forbidden'));
        }

        if ($department->doctors()->exists()) {
            return response()->json(['message' => __('messages.error')], 422);
        }

        $department->delete();

        return response()->json([
            'message' => __('messages.deleted', ['model' => __('common.department')]),
        ]);
    }
}
