<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{
    /**
     * Get user preferences (GET /api/calnest/preferences)
     */
    public function show(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $query = CalnestPreference::query();
        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        }

        $prefs = $query->pluck('value', 'key');
        return response()->json($prefs);
    }

    /**
     * Save user preferences (POST /api/calnest/preferences)
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;
        $data = $request->all();

        foreach ($data as $key => $value) {
            CalnestPreference::updateOrCreate(
                [
                    'user_id' => $userId,
                    'key' => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        }

        $query = CalnestPreference::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return response()->json([
            'message' => 'Preferences saved successfully',
            'preferences' => $query->pluck('value', 'key'),
        ]);
    }
}
