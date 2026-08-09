<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'technologies' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create();

        $project = $profile->projects()->create($request->only(['name', 'description', 'technologies']));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Project added successfully', 'project' => $project]);
        }

        return redirect()->back()->with('success', 'Project added successfully.');
    }

    public function update(Request $request, Project $project)
    {
        if ($project->profile_id !== auth()->user()->profile?->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'technologies' => 'nullable|string|max:255',
        ]);

        $project->update($request->only(['name', 'description', 'technologies']));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Project updated successfully', 'project' => $project]);
        }

        return redirect()->back()->with('success', 'Project updated successfully.');
    }

    public function destroy(Request $request, Project $project)
    {
        if ($project->profile_id !== auth()->user()->profile?->id) {
            abort(403);
        }

        $project->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Project deleted successfully']);
        }

        return redirect()->back()->with('success', 'Project deleted successfully.');
    }
}
