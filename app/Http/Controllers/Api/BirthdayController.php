<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestManualBirthday;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BirthdayController extends Controller
{
    /**
     * List user manual birthdays (GET /api/calnest/manual-birthdays)
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $birthdays = CalnestManualBirthday::where('user_id', $userId)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'items' => $birthdays->map(fn (CalnestManualBirthday $b) => $b->toResponseArray())->values(),
            'total' => $birthdays->count(),
        ], 200);
    }

    /**
     * Create manual birthday (POST /api/calnest/manual-birthdays)
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|string', // YYYY-MM-DD (1900 if year unknown)
            'phone' => 'nullable|string',
            'relationship' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $birthday = CalnestManualBirthday::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'date' => $validated['date'],
            'phone' => $validated['phone'] ?? null,
            'relationship' => $validated['relationship'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json($birthday->toResponseArray(), 201);
    }

    /**
     * Update manual birthday (PUT/PATCH /api/calnest/manual-birthdays/{id})
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $userId = $request->user()->id;
        $birthday = CalnestManualBirthday::where('user_id', $userId)->where('id', $id)->first();

        if (!$birthday) {
            return response()->json(['message' => 'Birthday not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|string',
            'phone' => 'nullable|string',
            'relationship' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $birthday->update($validated);

        return response()->json($birthday->fresh()->toResponseArray(), 200);
    }

    /**
     * Delete manual birthday (DELETE /api/calnest/manual-birthdays/{id})
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $userId = $request->user()->id;
        $birthday = CalnestManualBirthday::where('user_id', $userId)->where('id', $id)->first();

        if (!$birthday) {
            return response()->json(['message' => 'Birthday not found'], 404);
        }

        $birthday->delete();

        return response()->json([
            'status' => 'deleted',
        ], 200);
    }
}
