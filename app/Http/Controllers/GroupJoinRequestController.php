<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\StudyGroupMember;
use App\Services\GroupJoinRequestService;
use Illuminate\Http\Request;

class GroupJoinRequestController extends Controller
{
    public function __construct(
        protected GroupJoinRequestService $joinService
    ) {}

    /**
     * Show a single study group's detail page.
     */
    public function show(StudyGroup $group)
    {
        $user = auth()->user();

        $membershipState = $this->joinService->getMembershipState($group, $user);
        $activeCount     = $group->activeMembersCount();
        $pendingCount    = $group->pendingMembersCount();

        $group->loadMissing(['creator', 'memberships' => function ($q) {
            $q->where('status', 'active')->with('user');
        }]);

        return view('groups.show', compact(
            'group',
            'membershipState',
            'activeCount',
            'pendingCount'
        ));
    }

    /**
     * Process a student's request to join a study group.
     */
    public function sendRequest(StudyGroup $group)
    {
        $result = $this->joinService->sendRequest($group, auth()->user());

        return redirect()
            ->route('groups.show', $group)
            ->with($result['status'], $result['message']);
    }

    /**
     * Cancel a pending join request.
     */
    public function cancelRequest(StudyGroup $group)
    {
        $result = $this->joinService->cancelRequest($group, auth()->user());

        return redirect()
            ->route('groups.show', $group)
            ->with($result['status'], $result['message']);
    }

    /**
     * Show the Group Members & Pending Requests management page (matches UI Screenshot 1).
     */
    public function membersIndex(Request $request, StudyGroup $group)
    {
        $tab = $request->query('tab', 'all'); // 'all', 'admins', 'pending'
        $search = $request->query('search', '');

        $query = $group->memberships()->with('user');

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($tab === 'admins') {
            $query->where('role', 'admin')->where('status', 'active');
        } elseif ($tab === 'pending') {
            $query->where('status', 'pending');
        } else {
            // all members (active + pending or active)
            $query->whereIn('status', ['active', 'pending']);
        }

        $members = $query->orderBy('created_at', 'desc')->get();

        $counts = [
            'all'     => $group->memberships()->count(),
            'admins'  => $group->memberships()->where('role', 'admin')->where('status', 'active')->count(),
            'pending' => $group->memberships()->where('status', 'pending')->count(),
            'active'  => $group->memberships()->where('status', 'active')->count(),
        ];

        $existingMemberUserIds = $group->memberships()->pluck('user_id')->toArray();
        $invitableUsers = \App\Models\User::with('profile')
            ->whereNotIn('id', $existingMemberUserIds)
            ->orderBy('name')
            ->get();

        $isAdmin = $group->isAdmin(auth()->user());

        return view('groups.members', compact('group', 'members', 'tab', 'search', 'counts', 'isAdmin', 'invitableUsers'));
    }

    /**
     * Approve a pending join request.
     */
    public function approveRequest(StudyGroup $group, StudyGroupMember $member)
    {
        $result = $this->joinService->approveRequest($group, $member, auth()->user());

        return redirect()
            ->route('groups.members', ['group' => $group, 'tab' => 'pending'])
            ->with($result['status'], $result['message']);
    }

    /**
     * Reject a pending join request.
     */
    public function rejectRequest(StudyGroup $group, StudyGroupMember $member)
    {
        $result = $this->joinService->rejectRequest($group, $member, auth()->user());

        return redirect()
            ->route('groups.members', ['group' => $group, 'tab' => 'pending'])
            ->with($result['status'], $result['message']);
    }
}
