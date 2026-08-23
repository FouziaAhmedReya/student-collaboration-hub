<?php

namespace App\Http\Controllers\Modules\Rayhan;

use App\Http\Controllers\Controller;
use App\Models\StudyGroup;
use App\Models\StudyGroupMember;
use Illuminate\Http\Request;

class StudyGroupController extends Controller
{
    /**
     * Display a listing of study groups with filters & search.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $query = StudyGroup::with(['creator.profile', 'memberships.user.profile']);

        // Search across name, course, description, creator name
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                      $creatorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if ($filter === 'my') {
            $query->where(function ($q) use ($userId) {
                $q->where('creator_id', $userId)
                  ->orWhereHas('memberships', function ($mQuery) use ($userId) {
                      $mQuery->where('user_id', $userId)->where('status', 'active');
                  });
            });
        } elseif ($filter === 'public') {
            $query->where('visibility', 'public');
        } elseif ($filter === 'private') {
            $query->where('visibility', 'private');
        }

        $groups = $query->latest()->get();

        // Calculate count badges
        $counts = [
            'all' => StudyGroup::count(),
            'my' => StudyGroup::where('creator_id', $userId)
                ->orWhereHas('memberships', function ($mQuery) use ($userId) {
                    $mQuery->where('user_id', $userId)->where('status', 'active');
                })->count(),
            'public' => StudyGroup::where('visibility', 'public')->count(),
            'private' => StudyGroup::where('visibility', 'private')->count(),
        ];

        return view('groups.index', compact('groups', 'filter', 'search', 'counts'));
    }

    /**
     * Show the form for creating a new study group.
     */
    public function create()
    {
        $suggestedCourses = [
            'CSE470 - Software Engineering',
            'CSE471 - System Analysis and Design',
            'CSE422 - Artificial Intelligence',
            'CSE370 - Database Systems',
            'CSE220 - Data Structures',
            'CSE221 - Algorithms',
            'CSE321 - Operating Systems',
            'CSE341 - Microprocessors',
            'MAT110 - Differential Calculus',
            'MAT120 - Integral Calculus & Differential Equations',
            'PHY111 - Principles of Physics',
            'ENG101 - English Fundamentals',
            'BUS101 - Introduction to Business'
        ];

        return view('groups.create', compact('suggestedCourses'));
    }

    /**
     * Store a newly created study group in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'max_members' => 'required|integer|min:2|max:100',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'description' => 'required|string',
            'visibility' => 'required|in:public,private',
            'location_name' => 'nullable|string|max:255',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['creator_id'] = auth()->id();

        $group = StudyGroup::create($validated);

        // Creator automatically joins as active admin
        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => auth()->id(),
            'role' => 'admin',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return redirect()->route('groups.index')->with('success', "Study group '{$group->name}' created successfully!");
    }

    /**
     * Show the form for editing the specified study group.
     */
    public function edit(StudyGroup $group)
    {
        if (!$group->isAdmin(auth()->user())) {
            abort(403, 'Unauthorized action. Only group administrators can edit this group.');
        }

        $suggestedCourses = [
            'CSE470 - Software Engineering',
            'CSE471 - System Analysis and Design',
            'CSE422 - Artificial Intelligence',
            'CSE370 - Database Systems',
            'CSE220 - Data Structures',
            'CSE221 - Algorithms',
            'CSE321 - Operating Systems',
            'CSE341 - Microprocessors',
            'MAT110 - Differential Calculus',
            'MAT120 - Integral Calculus & Differential Equations',
            'PHY111 - Principles of Physics',
            'ENG101 - English Fundamentals',
            'BUS101 - Introduction to Business'
        ];

        return view('groups.edit', compact('group', 'suggestedCourses'));
    }

    /**
     * Update the specified study group in storage.
     */
    public function update(Request $request, StudyGroup $group)
    {
        if (!$group->isAdmin(auth()->user())) {
            abort(403, 'Unauthorized action. Only group administrators can update this group.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'max_members' => 'required|integer|min:2|max:100',
            'meeting_date' => 'required|date',
            'meeting_time' => 'required',
            'description' => 'required|string',
            'visibility' => 'required|in:public,private',
            'location_name' => 'nullable|string|max:255',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $group->update($validated);

        return redirect()->route('groups.index')->with('success', "Study group '{$group->name}' updated successfully!");
    }

    /**
     * Remove the specified study group from storage.
     */
    public function destroy(StudyGroup $group)
    {
        if (!$group->isAdmin(auth()->user())) {
            abort(403, 'Unauthorized action. Only group administrators can delete this group.');
        }

        $groupName = $group->name;
        $group->delete();

        return redirect()->route('groups.index')->with('success', "Study group '{$groupName}' deleted successfully.");
    }

    /**
     * Join a public group or request to join a private group.
     */
    public function join(StudyGroup $group)
    {
        $user = auth()->user();

        $existing = $group->memberships()->where('user_id', $user->id)->first();
        if ($existing) {
            if ($existing->status === 'active') {
                return redirect()->back()->with('info', 'You are already a member of this study group.');
            } else {
                return redirect()->back()->with('info', 'Your request to join this group is pending approval.');
            }
        }

        if ($group->hasReachedMaxMembers()) {
            return redirect()->back()->with('error', 'Cannot join. This study group has reached its maximum member limit.');
        }

        if ($group->visibility === 'public') {
            StudyGroupMember::create([
                'study_group_id' => $group->id,
                'user_id' => $user->id,
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]);

            return redirect()->back()->with('success', "You have joined '{$group->name}'!");
        } else {
            // Private group -> request pending
            StudyGroupMember::create([
                'study_group_id' => $group->id,
                'user_id' => $user->id,
                'role' => 'member',
                'status' => 'pending',
                'joined_at' => null,
            ]);

            return redirect()->back()->with('success', "Join request sent for '{$group->name}'. Awaiting group admin approval.");
        }
    }

    /**
     * Leave a study group.
     */
    public function leave(StudyGroup $group)
    {
        $user = auth()->user();

        if ($group->isCreator($user)) {
            return redirect()->back()->with('error', 'Group creators cannot leave the group. You can delete the group if you wish.');
        }

        $membership = $group->memberships()->where('user_id', $user->id)->first();
        if (!$membership) {
            return redirect()->back()->with('error', 'You are not a member of this group.');
        }

        $membership->delete();

        return redirect()->back()->with('success', "You have left '{$group->name}'.");
    }
}
