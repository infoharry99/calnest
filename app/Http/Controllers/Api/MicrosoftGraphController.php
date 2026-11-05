<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MicrosoftAccount;
use App\Models\MicrosoftCalendar;
use App\Models\MicrosoftCalendarEvent;
use App\Models\MicrosoftTodoList;
use App\Models\MicrosoftTodoTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class MicrosoftGraphController extends Controller
{
    /**
     * Reads the signed-in user's profile.
     * GET /me
     */
    public function getProfile(): JsonResponse
    {
        $account = MicrosoftAccount::first();

        if (!$account) {
            $account = MicrosoftAccount::create([
                'id' => 'a1b2c3d4',
                'displayName' => 'Ravi Patel',
                'mail' => 'ravi@outlook.com',
                'userPrincipalName' => 'ravi@outlook.com',
            ]);
        }

        return response()->json($account->toGraphArray(), 200);
    }

    /**
     * Lists Outlook calendars.
     * GET /me/calendars
     */
    public function listCalendars(Request $request): JsonResponse
    {
        $calendars = MicrosoftCalendar::all();

        return response()->json([
            'value' => $calendars->map->toGraphArray(),
        ], 200);
    }

    /**
     * Loads events for the primary calendar in the visible range.
     * GET /me/calendarView
     */
    public function getPrimaryCalendarView(Request $request): JsonResponse
    {
        $primary = MicrosoftCalendar::where('isDefaultCalendar', true)->first()
            ?? MicrosoftCalendar::first();

        if (!$primary) {
            return response()->json(['value' => []], 200);
        }

        return $this->getCalendarViewEvents($request, $primary->id);
    }

    /**
     * Loads events for a specific Outlook calendar.
     * GET /me/calendars/{calendarId}/calendarView
     */
    public function getCalendarView(Request $request, string $calendarId): JsonResponse
    {
        $calendarId = urldecode($calendarId);
        $calendar = MicrosoftCalendar::find($calendarId);

        if (!$calendar) {
            return response()->json([
                'error' => [
                    'code' => 'ResourceNotFound',
                    'message' => 'Calendar not found.',
                ]
            ], 404);
        }

        return $this->getCalendarViewEvents($request, $calendarId);
    }

    /**
     * Helper for calendarView querying
     */
    private function getCalendarViewEvents(Request $request, string $calendarId): JsonResponse
    {
        $query = MicrosoftCalendarEvent::where('calendar_id', $calendarId);

        $startDateTime = $request->query('startDateTime');
        if ($startDateTime) {
            $startDateTime = urldecode($startDateTime);
            $query->where('end_dateTime', '>=', $startDateTime);
        }

        $endDateTime = $request->query('endDateTime');
        if ($endDateTime) {
            $endDateTime = urldecode($endDateTime);
            $query->where('start_dateTime', '<=', $endDateTime);
        }

        $top = (int) $request->query('$top', 500);
        $events = $query->take($top)->get();

        $select = $request->query('$select') ? explode(',', $request->query('$select')) : null;

        return response()->json([
            'value' => $events->map(fn($e) => $e->toGraphArray($select)),
        ], 200);
    }

    /**
     * Creates an Outlook event.
     * POST /me/calendars/{calendarId}/events
     */
    public function createEvent(Request $request, string $calendarId): JsonResponse
    {
        $calendarId = urldecode($calendarId);
        $calendar = MicrosoftCalendar::find($calendarId);

        if (!$calendar) {
            return response()->json([
                'error' => [
                    'code' => 'ResourceNotFound',
                    'message' => 'Calendar not found.',
                ]
            ], 404);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'isAllDay' => 'nullable|boolean',
            'body.contentType' => 'nullable|string',
            'body.content' => 'nullable|string',
            'location.displayName' => 'nullable|string',
            'start.dateTime' => 'required|string',
            'start.timeZone' => 'nullable|string',
            'end.dateTime' => 'required|string',
            'end.timeZone' => 'nullable|string',
        ]);

        $eventId = 'AAMkAGI2-evt' . Str::lower(Str::random(6));

        $event = MicrosoftCalendarEvent::create([
            'id' => $eventId,
            'calendar_id' => $calendarId,
            'subject' => $validated['subject'],
            'isAllDay' => $validated['isAllDay'] ?? false,
            'body_contentType' => $validated['body']['contentType'] ?? 'text',
            'body_content' => $validated['body']['content'] ?? null,
            'bodyPreview' => Str::limit($validated['body']['content'] ?? '', 100),
            'location_displayName' => $validated['location']['displayName'] ?? null,
            'start_dateTime' => $validated['start']['dateTime'],
            'start_timeZone' => $validated['start']['timeZone'] ?? 'UTC',
            'end_dateTime' => $validated['end']['dateTime'],
            'end_timeZone' => $validated['end']['timeZone'] ?? 'UTC',
        ]);

        return response()->json([
            'id' => $event->id,
            'subject' => $event->subject,
            'isAllDay' => (bool)$event->isAllDay,
        ], 201);
    }

    /**
     * Saves edits to an Outlook event.
     * PATCH /me/calendars/{calendarId}/events/{eventId}
     */
    public function updateEvent(Request $request, string $calendarId, string $eventId): JsonResponse
    {
        $calendarId = urldecode($calendarId);
        $eventId = urldecode($eventId);

        $event = MicrosoftCalendarEvent::where('calendar_id', $calendarId)
            ->where('id', $eventId)
            ->first();

        if (!$event) {
            return response()->json([
                'error' => [
                    'code' => 'ResourceNotFound',
                    'message' => 'Event not found.',
                ]
            ], 404);
        }

        if ($request->has('subject')) {
            $event->subject = $request->input('subject');
        }
        if ($request->has('isAllDay')) {
            $event->isAllDay = $request->input('isAllDay');
        }
        if ($request->has('start.dateTime')) {
            $event->start_dateTime = $request->input('start.dateTime');
        }
        if ($request->has('start.timeZone')) {
            $event->start_timeZone = $request->input('start.timeZone');
        }
        if ($request->has('end.dateTime')) {
            $event->end_dateTime = $request->input('end.dateTime');
        }
        if ($request->has('end.timeZone')) {
            $event->end_timeZone = $request->input('end.timeZone');
        }
        if ($request->has('location.displayName')) {
            $event->location_displayName = $request->input('location.displayName');
        }
        if ($request->has('body.content')) {
            $event->body_content = $request->input('body.content');
            $event->bodyPreview = Str::limit($request->input('body.content'), 100);
        }

        $event->save();

        return response()->json([
            'id' => $event->id,
            'subject' => $event->subject,
        ], 200);
    }

    /**
     * Deletes an Outlook event.
     * DELETE /me/calendars/{calendarId}/events/{eventId}
     */
    public function deleteEvent(string $calendarId, string $eventId): Response
    {
        $calendarId = urldecode($calendarId);
        $eventId = urldecode($eventId);

        $event = MicrosoftCalendarEvent::where('calendar_id', $calendarId)
            ->where('id', $eventId)
            ->first();

        if ($event) {
            $event->delete();
        }

        return response()->noContent();
    }

    /**
     * Lists Microsoft To Do lists.
     * GET /me/todo/lists
     */
    public function listTodoLists(): JsonResponse
    {
        $lists = MicrosoftTodoList::all();

        return response()->json([
            'value' => $lists->map->toGraphArray(),
        ], 200);
    }

    /**
     * Loads tasks in a To Do list.
     * GET /me/todo/lists/{listId}/tasks
     */
    public function listTodoTasks(Request $request, string $listId): JsonResponse
    {
        $listId = urldecode($listId);
        $top = (int) $request->query('$top', 200);

        $tasks = MicrosoftTodoTask::where('list_id', $listId)
            ->take($top)
            ->get();

        return response()->json([
            'value' => $tasks->map->toGraphArray(),
        ], 200);
    }

    /**
     * Creates a To Do task.
     * POST /me/todo/lists/{listId}/tasks
     */
    public function createTodoTask(Request $request, string $listId): JsonResponse
    {
        $listId = urldecode($listId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'nullable|string|in:notStarted,completed,inProgress',
            'body.contentType' => 'nullable|string',
            'body.content' => 'nullable|string',
            'dueDateTime.dateTime' => 'nullable|string',
            'dueDateTime.timeZone' => 'nullable|string',
        ]);

        $taskId = 'AQMkADAw-t' . Str::lower(Str::random(4));

        $task = MicrosoftTodoTask::create([
            'id' => $taskId,
            'list_id' => $listId,
            'title' => $validated['title'],
            'status' => $validated['status'] ?? 'notStarted',
            'body_contentType' => $validated['body']['contentType'] ?? 'text',
            'body_content' => $validated['body']['content'] ?? '',
            'due_dateTime' => $validated['dueDateTime']['dateTime'] ?? null,
            'due_timeZone' => $validated['dueDateTime']['timeZone'] ?? 'UTC',
        ]);

        return response()->json([
            'id' => $task->id,
            'title' => $task->title,
            'status' => $task->status,
        ], 201);
    }

    /**
     * Edits a To Do task, or sets status.
     * PATCH /me/todo/lists/{listId}/tasks/{taskId}
     */
    public function updateTodoTask(Request $request, string $listId, string $taskId): JsonResponse
    {
        $listId = urldecode($listId);
        $taskId = urldecode($taskId);

        $task = MicrosoftTodoTask::where('list_id', $listId)
            ->where('id', $taskId)
            ->first();

        if (!$task) {
            return response()->json([
                'error' => [
                    'code' => 'ResourceNotFound',
                    'message' => 'Task not found.',
                ]
            ], 404);
        }

        if ($request->has('status')) {
            $task->status = $request->input('status');
        }
        if ($request->has('title')) {
            $task->title = $request->input('title');
        }
        if ($request->has('body.content')) {
            $task->body_content = $request->input('body.content');
        }
        if ($request->has('dueDateTime.dateTime')) {
            $task->due_dateTime = $request->input('dueDateTime.dateTime');
        }

        $task->save();

        return response()->json([
            'id' => $task->id,
            'status' => $task->status,
        ], 200);
    }

    /**
     * Deletes a To Do task.
     * DELETE /me/todo/lists/{listId}/tasks/{taskId}
     */
    public function deleteTodoTask(string $listId, string $taskId): Response
    {
        $listId = urldecode($listId);
        $taskId = urldecode($taskId);

        $task = MicrosoftTodoTask::where('list_id', $listId)
            ->where('id', $taskId)
            ->first();

        if ($task) {
            $task->delete();
        }

        return response()->noContent();
    }
}
