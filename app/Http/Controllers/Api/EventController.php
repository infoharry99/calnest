<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestEvent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * List user events, optionally filtered by date range (GET /api/calnest/events)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = CalnestEvent::where('user_id', $user->id);

        $from = $request->query('from');
        $to = $request->query('to');

        if ($from && $to) {
            try {
                $fromBoundary = Carbon::parse($from)->startOfDay()->toIso8601String();
                $toBoundary = Carbon::parse($to)->endOfDay()->toIso8601String();

                $query->where('start', '<=', $toBoundary)
                      ->where('end', '>=', $fromBoundary);
            } catch (\Throwable $e) {
                // If parse fails, fallback to simple comparison
                $query->where('start', '<=', $to . 'T23:59:59')
                      ->where('end', '>=', $from . 'T00:00:00');
            }
        } elseif ($from) {
            try {
                $fromBoundary = Carbon::parse($from)->startOfDay()->toIso8601String();
                $query->where('end', '>=', $fromBoundary);
            } catch (\Throwable $e) {}
        } elseif ($to) {
            try {
                $toBoundary = Carbon::parse($to)->endOfDay()->toIso8601String();
                $query->where('start', '<=', $toBoundary);
            } catch (\Throwable $e) {}
        }

        $events = $query->orderBy('start', 'asc')->get();

        return response()->json([
            'items' => $events->map(fn (CalnestEvent $e) => $e->toResponseArray())->values(),
        ], 200);
    }

    /**
     * Create an event (POST /api/calnest/events)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start' => 'required|string',
            'end' => 'required|string',
            'is_all_day' => 'nullable|boolean',
            'color' => 'nullable|string|max:50',
        ]);

        $event = CalnestEvent::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'start' => $validated['start'],
            'end' => $validated['end'],
            'is_all_day' => $validated['is_all_day'] ?? false,
            'color' => $validated['color'] ?? '#3B82F6',
        ]);

        return response()->json($event->toResponseArray(), 201);
    }

    /**
     * Update an event (PUT/PATCH /api/calnest/events/{id})
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $event = CalnestEvent::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start' => 'sometimes|required|string',
            'end' => 'sometimes|required|string',
            'is_all_day' => 'nullable|boolean',
            'color' => 'nullable|string|max:50',
        ]);

        $event->update($validated);

        return response()->json($event->fresh()->toResponseArray(), 200);
    }

    /**
     * Delete an event (DELETE /api/calnest/events/{id})
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $event = CalnestEvent::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->delete();

        return response()->json([
            'status' => 'deleted',
        ], 200);
    }
}
