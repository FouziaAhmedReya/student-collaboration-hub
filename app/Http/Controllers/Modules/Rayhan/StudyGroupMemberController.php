<?php

namespace App\Http\Controllers\Modules\Rayhan;

use App\Http\Controllers\Controller;
use App\Models\StudyGroup;
use App\Models\StudyGroupMember;
use App\Models\User;
use Illuminate\Http\Request;

class StudyGroupMemberController extends Controller
{
    /**
     * Display members of a study group with tabs (All, Admins, Pending) and search.
     */
    public function index(Request $request, StudyGroup $group)
    {
        if (!$group->isAdmin(auth()->user())) {
            abort(403, 'Unauthorized. Only group administrators can manage group members.');
        }

        $tab = $request->get('tab', 'all');
        $search = $request->get('search');

        $query = $group->memberships()->with(['user.profile']);

        if (!empty($search)) {
            $query->whereHas('user', function ($uQuery) use ($search) {
                $uQuery->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($tab === 'admins') {
            $query->where('role', 'admin')->where('status', 'active');
        } elseif ($tab === 'pending') {
            $query->where('status', 'pending');
        }

        $members = $query->latest()->get();

        $counts = [
            'all' => $group->memberships()->count(),
            'admins' => $group->memberships()->where('role', 'admin')->where('status', 'active')->count(),
            'pending' => $group->memberships()->where('status', 'pending')->count(),
            'active' => $group->memberships()->where('status', 'active')->count(),
        ];

        // Potential users to invite (registered users who are not yet members)
        $existingMemberUserIds = $group->memberships()->pluck('user_id')->toArray();
        $invitableUsers = User::with('profile')
            ->whereNotIn('id', $existingMemberUserIds)
            ->orderBy('name')
            ->get();

        return view('groups.members', compact('group', 'members', 'tab', 'search', 'counts', 'invitableUsers'));
    }

    /**
     * Invite an existing registered student to the study group.
     */
    public function invite(Request $request, StudyGroup $group)
    {
        if (!$group->isAdmin(auth()->user())) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $targetUser = User::findOrFail($validated['user_id']);

        if ($group->memberships()->where('user_id', $targetUser->id)->exists()) {
            return redirect()->back()->with('error', "{$targetUser->name} is already a member or has a pending invitation.");
        }

        if ($group->hasReachedMaxMembers()) {
            return redirect()->back()->with('error', 'Cannot invite members. The group is currently full.');
        }

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $targetUser->id,
            'role' => 'member',
            'status' => 'pending',
            'joined_at' => null,
        ]);

        return redirect()->back()->with('success', "Invitation sent to {$targetUser->name}!");
    }

    /**
     * Activate or reject a pending member request.
     */
    public function updateStatus(Request $request, StudyGroup $group, StudyGroupMember $member)
    {
        if (!$group->isAdmin(auth()->user())) {
            abort(403, 'Unauthorized.');
        }

        if ($member->study_group_id !== $group->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,rejected',
        ]);

        if ($validated['status'] === 'active') {
            if ($group->hasReachedMaxMembers()) {
                return redirect()->back()->with('error', 'Cannot activate member. Group is already at maximum capacity.');
            }

            $member->update([
                'status' => 'active',
                'joined_at' => now(),
            ]);

            return redirect()->back()->with('success', "{$member->user->name} has been activated as an active member!");
        } elseif ($validated['status'] === 'rejected') {
            $memberName = $member->user->name;
            $member->delete();

            return redirect()->back()->with('info', "Join request from {$memberName} was declined.");
        }

        return redirect()->back();
    }

    /**
     * Promote or demote a member's role (Admin / Member).
     */
    public function updateRole(Request $request, StudyGroup $group, StudyGroupMember $member)
    {
        if (!$group->isAdmin(auth()->user())) {
            abort(403, 'Unauthorized.');
        }

        if ($member->study_group_id !== $group->id) {
            abort(404);
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,member',
        ]);

        if ($member->user_id === $group->creator_id && $validated['role'] === 'member') {
            return redirect()->back()->with('error', 'The primary group creator cannot be demoted from admin.');
        }

        $member->update([
            'role' => $validated['role'],
        ]);

        return redirect()->back()->with('success', "Role for {$member->user->name} updated to {$validated['role']}.");
    }

    /**
     * Remove a member from the group.
     */
    public function destroy(StudyGroup $group, StudyGroupMember $member)
    {
        if (!$group->isAdmin(auth()->user())) {
            abort(403, 'Unauthorized.');
        }

        if ($member->study_group_id !== $group->id) {
            abort(404);
        }

        if ($member->user_id === $group->creator_id) {
            return redirect()->back()->with('error', 'Cannot remove the primary group creator from the group.');
        }

        $userName = $member->user->name;
        $member->delete();

        return redirect()->back()->with('success', "{$userName} was removed from the study group.");
    }
}
