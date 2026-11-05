<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoogleCalendar;
use App\Models\GoogleCalendarEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class GoogleCalendarController extends Controller
{
    /**
     * Lists every calendar the user has.
     * GET /users/me/calendarList
     */
    public function listCalendars(): JsonResponse
    {
        $calendars = GoogleCalendar::all();

        return response()->json([
            'kind' => 'calendar#calendarList',
            'items' => $calendars->map->toGoogleArray(),
        ]);
    }

    /**
     * Loads events for the visible range.
     * GET /calendars/{calendarId}/events
     */
    public function listEvents(Request $request, string $calendarId): JsonResponse
    {
        $calendar = GoogleCalendar::find($calendarId);
        $summary = $calendar ? $calendar->summary : $calendarId;

        $query = GoogleCalendarEvent::where('calendar_id', $calendarId);

        if ($request->filled('timeMin')) {
            $timeMin = $request->query('timeMin');
            $dateMin = substr($timeMin, 0, 10);
            $query->where(function ($q) use ($timeMin, $dateMin) {
                $q->where('start_dateTime', '>=', $timeMin)
                  ->orWhere('start_date', '>=', $dateMin);
            });
        }

        if ($request->filled('timeMax')) {
            $timeMax = $request->query('timeMax');
            $dateMax = substr($timeMax, 0, 10);
            $query->where(function ($q) use ($timeMax, $dateMax) {
                $q->where('end_dateTime', '<=', $timeMax)
                  ->orWhere('end_date', '<=', $dateMax);
            });
        }

        $orderBy = $request->query('orderBy');
        if ($orderBy === 'startTime') {
            $query->orderByRaw('COALESCE(start_dateTime, start_date) ASC');
        }

        $maxResults = (int) $request->query('maxResults', 250);
        $events = $query->take($maxResults)->get();

        return response()->json([
            'kind' => 'calendar#events',
            'summary' => $summary,
            'items' => $events->map->toGoogleArray(),
        ]);
    }

    /**
     * Creates an event.
     * POST /calendars/{calendarId}/events
     */
    public function createEvent(Request $request, string $calendarId): JsonResponse
    {
        $calendar = GoogleCalendar::find($calendarId);
        if ($calendar && $calendar->accessRole === 'reader') {
            return response()->json([
                'error' => [
                    'code' => 403,
                    'message' => 'The user does not have sufficient permissions for this operation (read-only calendar).',
                ]
            ], 403);
        }

        $validated = $request->validate([
            'summary' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'colorId' => 'nullable|string',
            'start' => 'required|array',
            'end' => 'required|array',
            'start.dateTime' => 'nullable|string',
            'start.date' => 'nullable|string',
            'end.dateTime' => 'nullable|string',
            'end.date' => 'nullable|string',
        ]);

        $eventId = Str::lower(Str::random(10));
        $htmlLink = "https://www.google.com/calendar/event?eid=" . base64_encode($eventId);

        $event = GoogleCalendarEvent::create([
            'id' => $eventId,
            'calendar_id' => $calendarId,
            'status' => 'confirmed',
            'summary' => $validated['summary'],
            'description' => $validated['description'] ?? null,
            'location' => $validated['location'] ?? null,
            'htmlLink' => $htmlLink,
            'start_dateTime' => $validated['start']['dateTime'] ?? null,
            'start_date' => $validated['start']['date'] ?? null,
            'end_dateTime' => $validated['end']['dateTime'] ?? null,
            'end_date' => $validated['end']['date'] ?? null,
            'colorId' => $validated['colorId'] ?? null,
        ]);

        return response()->json([
            'id' => $event->id,
            'status' => $event->status,
            'htmlLink' => $event->htmlLink,
            'summary' => $event->summary,
            'start' => array_filter([
                'dateTime' => $event->start_dateTime,
                'date' => $event->start_date,
            ]),
            'end' => array_filter([
                'dateTime' => $event->end_dateTime,
                'date' => $event->end_date,
            ]),
        ], 200);
    }

    /**
     * Saves edits to an event.
     * PATCH /calendars/{calendarId}/events/{eventId}
     */
    public function updateEvent(Request $request, string $calendarId, string $eventId): JsonResponse
    {
        $event = GoogleCalendarEvent::where('calendar_id', $calendarId)
            ->where('id', $eventId)
            ->first();

        if (!$event) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => 'Event not found.',
                ]
            ], 404);
        }

        if ($request->has('summary')) {
            $event->summary = $request->input('summary');
        }
        if ($request->has('description')) {
            $event->description = $request->input('description');
        }
        if ($request->has('location')) {
            $event->location = $request->input('location');
        }
        if ($request->has('start.dateTime')) {
            $event->start_dateTime = $request->input('start.dateTime');
        }
        if ($request->has('start.date')) {
            $event->start_date = $request->input('start.date');
        }
        if ($request->has('end.dateTime')) {
            $event->end_dateTime = $request->input('end.dateTime');
        }
        if ($request->has('end.date')) {
            $event->end_date = $request->input('end.date');
        }
        if ($request->has('colorId')) {
            $event->colorId = $request->input('colorId');
        }

        $event->save();

        return response()->json([
            'id' => $event->id,
            'summary' => $event->summary,
            'status' => $event->status,
        ]);
    }

    /**
     * Deletes an event.
     * DELETE /calendars/{calendarId}/events/{eventId}
     */
    public function deleteEvent(string $calendarId, string $eventId): Response
    {
        $event = GoogleCalendarEvent::where('calendar_id', $calendarId)
            ->where('id', $eventId)
            ->first();

        if ($event) {
            $event->delete();
        }

        // 204 No Content. 404 or 410 is treated as success according to doc.
        return response()->noContent();
    }
}
