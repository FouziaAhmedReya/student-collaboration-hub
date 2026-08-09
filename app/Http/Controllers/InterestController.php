<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interest;

class InterestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create();

        $interest = $profile->interests()->create($request->only(['name']));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Interest added successfully', 'interest' => $interest]);
        }

        return redirect()->back()->with('success', 'Interest added successfully.');
    }

    public function destroy(Request $request, Interest $interest)
    {
        if ($interest->profile_id !== auth()->user()->profile?->id) {
            abort(403);
        }

        $interest->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Interest deleted successfully']);
        }

        return redirect()->back()->with('success', 'Interest deleted successfully.');
    }
}
