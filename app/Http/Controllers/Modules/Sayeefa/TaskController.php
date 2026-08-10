<?php

namespace App\Http\Controllers\Modules\Sayeefa;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // POST /api/tasks
    public function createTask(Request $request)
    {
        $validated = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'task_name'   => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,id',
            'deadline'    => 'required|date_format:Y-m-d H:i:s',
        ]);

        $task = Task::create([
            'project_id'  => $validated['project_id'],
            'task_name'   => $validated['task_name'],
            'assigned_to' => $validated['assigned_to'],
            'deadline'    => $validated['deadline'],
            'status'      => 'Pending',
            // TODO: replace with a real Google Calendar API call
            'google_calendar_event_id' => 'cal_evt_' . uniqid(),
        ]);

        return response()->json([
            'message' => 'Task created successfully and synced with Google Calendar!',
            'task' => $task,
        ], 201);
    }

    // GET /api/projects/{projectId}/tasks
    public function getProjectTasks($projectId)
    {
        $tasks = Task::where('project_id', $projectId)
            ->with('assignee:id,name')
            ->get();

        return response()->json($tasks, 200);
    }

    // PATCH /api/tasks/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,In Progress,Done',
        ]);

        $task = Task::findOrFail($id);
        $task->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Task status updated',
            'task' => $task,
        ], 200);
    }

    // DELETE /api/tasks/{id}
    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted'], 200);
    }
}