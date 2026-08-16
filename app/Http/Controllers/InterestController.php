<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interest;
use App\Models\DepartmentInterest;

class InterestController extends Controller
{
    /**
     * Return the department-filtered interest suggestions as JSON.
     * Used by the Add Interest modal via fetch().
     */
    public function suggestions(Request $request)
    {
        $department = $request->query('department', 'General');

        $suggestions = DepartmentInterest::forDepartment($department)
            ->pluck('name');

        return response()->json($suggestions);
    }

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
