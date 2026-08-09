<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortfolioLink;

class PortfolioLinkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ]);

        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create();

        $portfolioLink = $profile->portfolioLinks()->create($request->only(['platform', 'url']));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Portfolio link added successfully', 'portfolioLink' => $portfolioLink]);
        }

        return redirect()->back()->with('success', 'Portfolio link added successfully.');
    }

    public function update(Request $request, PortfolioLink $portfolioLink)
    {
        if ($portfolioLink->profile_id !== auth()->user()->profile?->id) {
            abort(403);
        }

        $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ]);

        $portfolioLink->update($request->only(['platform', 'url']));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Portfolio link updated successfully', 'portfolioLink' => $portfolioLink]);
        }

        return redirect()->back()->with('success', 'Portfolio link updated successfully.');
    }

    public function destroy(Request $request, PortfolioLink $portfolioLink)
    {
        if ($portfolioLink->profile_id !== auth()->user()->profile?->id) {
            abort(403);
        }

        $portfolioLink->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Portfolio link deleted successfully']);
        }

        return redirect()->back()->with('success', 'Portfolio link deleted successfully.');
    }
}
