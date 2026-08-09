<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;

class SkillController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'proficiency' => 'required|integer|min:0|max:100',
        ]);

        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create();

        $skill = $profile->skills()->create($request->only(['name', 'proficiency']));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Skill added successfully', 'skill' => $skill]);
        }

        return redirect()->back()->with('success', 'Skill added successfully.');
    }

    public function update(Request $request, Skill $skill)
    {
        if ($skill->profile_id !== auth()->user()->profile?->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'proficiency' => 'required|integer|min:0|max:100',
        ]);

        $skill->update($request->only(['name', 'proficiency']));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Skill updated successfully', 'skill' => $skill]);
        }

        return redirect()->back()->with('success', 'Skill updated successfully.');
    }

    public function destroy(Request $request, Skill $skill)
    {
        if ($skill->profile_id !== auth()->user()->profile?->id) {
            abort(403);
        }

        $skill->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Skill deleted successfully']);
        }

        return redirect()->back()->with('success', 'Skill deleted successfully.');
    }
}
