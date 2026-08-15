<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locales = config('app.available_locales', ['ar' => 'Arabic', 'en' => 'English']);
        return response()->json(['data' => $locales]);
    }

    public function translations(Request $request): JsonResponse
    {
        $request->validate(['group' => 'sometimes|string']);

        $query = Translation::query();
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $translations = $query->paginate($request->per_page ?? 50);

        return response()->json([
            'data' => $translations,
            'meta' => [
                'current_page' => $translations->currentPage(),
                'last_page' => $translations->lastPage(),
                'total' => $translations->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'group' => 'required|string|max:100',
            'ar' => 'required|string',
            'en' => 'required|string',
        ]);

        $translation = Translation::updateOrCreate(
            ['key' => $validated['key'], 'group' => $validated['group']],
            ['ar' => $validated['ar'], 'en' => $validated['en']]
        );

        return response()->json([
            'message' => __('messages.created', ['model' => 'Translation']),
            'data' => $translation,
        ], 201);
    }

    public function update(Request $request, Translation $translation): JsonResponse
    {
        $validated = $request->validate([
            'ar' => 'sometimes|string',
            'en' => 'sometimes|string',
        ]);

        $translation->update($validated);

        return response()->json([
            'message' => __('messages.updated', ['model' => 'Translation']),
            'data' => $translation->fresh(),
        ]);
    }

    public function setDefaultLanguage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language' => 'required|in:ar,en',
        ]);

        // Update system default
        config(['app.locale' => $validated['language']]);

        return response()->json([
            'message' => __('auth.language_changed'),
            'data' => ['default_language' => $validated['language']],
        ]);
    }
}
