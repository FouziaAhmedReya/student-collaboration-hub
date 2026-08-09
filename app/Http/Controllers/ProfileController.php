<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $user->load('profile.skills', 'profile.interests', 'profile.projects', 'profile.portfolioLinks');

        $profile = $user->profile;
        if (!$profile) {
            $profile = $user->profile()->create();
            $user->load('profile');
            $profile = $user->profile;
        }

        $completionPercentage = $profile->getCompletionPercentage();

        return view('profile.show', compact('user', 'profile', 'completionPercentage'));
    }

    public function edit()
    {
        $user = auth()->user();
        $user->load('profile');
        $profile = $user->profile ?? $user->profile()->create();

        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'semester' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'joined_date' => 'nullable|date',
            'about_me' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = auth()->user();
        $user->update(['name' => $request->name]);

        $profile = $user->profile ?? $user->profile()->create();

        $profileData = $request->only(['department', 'semester', 'university', 'joined_date', 'about_me']);

        if ($request->hasFile('profile_photo')) {
            if ($profile->profile_photo) {
                Storage::disk('public')->delete($profile->profile_photo);
            }
            $profileData['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $profile->update($profileData);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'preferred_location_name' => 'nullable|string|max:255',
            'preferred_location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create();

        $profile->update($request->only([
            'preferred_location_name',
            'preferred_location_address',
            'latitude',
            'longitude'
        ]));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Location updated successfully',
                'profile' => $profile
            ]);
        }

        return redirect()->back()->with('success', 'Preferred study location updated successfully.');
    }
}
