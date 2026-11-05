<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoogleTask;
use App\Models\GoogleTaskList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class GoogleTasksController extends Controller
{
    /**
     * Lists the user's task lists.
     * GET /users/@me/lists
     */
    public function listTaskLists(): JsonResponse
    {
        $lists = GoogleTaskList::all();

        return response()->json([
            'kind' => 'tasks#taskLists',
            'items' => $lists->map->toGoogleArray(),
        ]);
    }

    /**
     * Loads tasks in a list.
     * GET /lists/{tasklistId}/tasks
     */
    public function listTasks(Request $request, string $tasklistId): JsonResponse
    {
        $maxResults = (int) $request->query('maxResults', 200);

        $tasks = GoogleTask::where('task_list_id', $tasklistId)
            ->take($maxResults)
            ->get();

        return response()->json([
            'kind' => 'tasks#tasks',
            'items' => $tasks->map->toGoogleArray(),
        ]);
    }

    /**
     * Creates a task.
     * POST /lists/{tasklistId}/tasks
     */
    public function createTask(Request $request, string $tasklistId): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'due' => 'nullable|string',
            'status' => 'nullable|string|in:needsAction,completed',
        ]);

        $taskId = 'dGFzay' . Str::lower(Str::random(4));

        $task = GoogleTask::create([
            'id' => $taskId,
            'task_list_id' => $tasklistId,
            'title' => $validated['title'],
            'notes' => $validated['notes'] ?? null,
            'due' => $validated['due'] ?? null,
            'status' => $validated['status'] ?? 'needsAction',
        ]);

        return response()->json($task->toGoogleArray(), 200);
    }

    /**
     * Edits a task, or marks it complete or open.
     * PATCH /lists/{tasklistId}/tasks/{taskId}
     */
    public function updateTask(Request $request, string $tasklistId, string $taskId): JsonResponse
    {
        $task = GoogleTask::where('task_list_id', $tasklistId)
            ->where('id', $taskId)
            ->first();

        if (!$task) {
            return response()->json([
                'error' => [
                    'code' => 404,
                    'message' => 'Task not found.',
                ]
            ], 404);
        }

        if ($request->has('title')) {
            $task->title = $request->input('title');
        }
        if ($request->has('notes')) {
            $task->notes = $request->input('notes');
        }
        if ($request->has('status')) {
            $task->status = $request->input('status');
        }
        if ($request->has('due')) {
            $task->due = $request->input('due');
        }
        if ($request->has('completed')) {
            $task->completed = $request->input('completed');
        } elseif ($task->status === 'completed' && !$task->completed) {
            $task->completed = now()->toISOString();
        }

        $task->save();

        return response()->json($task->toGoogleArray(), 200);
    }

    /**
     * Deletes a task.
     * DELETE /lists/{tasklistId}/tasks/{taskId}
     */
    public function deleteTask(string $tasklistId, string $taskId): Response
    {
        $task = GoogleTask::where('task_list_id', $tasklistId)
            ->where('id', $taskId)
            ->first();

        if ($task) {
            $task->delete();
        }

        return response()->noContent();
    }
}
