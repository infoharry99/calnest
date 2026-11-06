<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * List user tasks (GET /api/calnest/tasks)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = CalnestTask::where('user_id', $user->id);

        if ($request->has('completed')) {
            $query->where('completed', filter_var($request->query('completed'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->query('priority'));
        }

        $tasks = $query->orderBy('due', 'asc')->orderBy('id', 'desc')->get();

        return response()->json([
            'items' => $tasks->map(fn (CalnestTask $t) => $t->toResponseArray())->values(),
        ], 200);
    }

    /**
     * Create a task (POST /api/calnest/tasks)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high',
            'due' => 'nullable|string',
            'completed' => 'nullable|boolean',
        ]);

        $task = CalnestTask::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'priority' => $validated['priority'] ?? 'medium',
            'due' => $validated['due'] ?? null,
            'completed' => $validated['completed'] ?? false,
        ]);

        return response()->json($task->toResponseArray(), 201);
    }

    /**
     * Update task / mark complete (PUT/PATCH /api/calnest/tasks/{id})
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $task = CalnestTask::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high',
            'due' => 'nullable|string',
            'completed' => 'nullable|boolean',
        ]);

        $task->update($validated);

        return response()->json($task->fresh()->toResponseArray(), 200);
    }

    /**
     * Delete a task (DELETE /api/calnest/tasks/{id})
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $task = CalnestTask::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json([
            'status' => 'deleted',
        ], 200);
    }
}
