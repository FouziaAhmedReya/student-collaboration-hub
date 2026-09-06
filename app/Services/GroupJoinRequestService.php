<?php

namespace App\Services;

use App\Models\StudyGroup;
use App\Models\StudyGroupMember;
use App\Models\User;
use Illuminate\Support\Collection;

class GroupJoinRequestService
{
    /**
     * Determine the current membership status of a user relative to a group.
     * Returns: 'creator' | 'admin' | 'active' | 'pending' | 'none'
     */
    public function getMembershipState(StudyGroup $group, ?User $user): string
    {
        if (! $user) {
            return 'guest';
        }

        if ($group->isCreator($user)) {
            return 'creator';
        }

        $membership = $group->memberships()
            ->where('user_id', $user->id)
            ->first();

        if (! $membership) {
            return 'none';
        }

        if ($membership->status === 'pending') {
            return 'pending';
        }

        if ($membership->status === 'active') {
            return $membership->role === 'admin' ? 'admin' : 'active';
        }

        return 'none';
    }

    /**
     * Process a student's request to join a study group.
     */
    public function sendRequest(StudyGroup $group, User $user): array
    {
        if ($group->isCreator($user)) {
            return ['status' => 'error', 'message' => 'You are the creator of this group.'];
        }

        $existing = $group->memberships()->where('user_id', $user->id)->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return ['status' => 'info', 'message' => 'You are already a member of this group.'];
            }
            if ($existing->status === 'pending') {
                return ['status' => 'info', 'message' => 'Your join request is already pending approval.'];
            }
        }

        if ($group->hasReachedMaxMembers()) {
            return ['status' => 'error', 'message' => 'This group has already reached its maximum capacity.'];
        }

        // If the group is public, student request is placed into pending status for admin review
        $status = 'pending';
        $joinedAt = null;

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id'        => $user->id,
            'role'           => 'member',
            'status'         => $status,
            'joined_at'      => $joinedAt,
        ]);

        return [
            'status'  => 'success',
            'message' => 'Join request sent successfully! An administrator will review your request.',
        ];
    }

    /**
     * Cancel a student's own pending join request.
     */
    public function cancelRequest(StudyGroup $group, User $user): array
    {
        $membership = $group->memberships()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (! $membership) {
            return ['status' => 'error', 'message' => 'No pending request found for this group.'];
        }

        $membership->delete();

        return ['status' => 'info', 'message' => 'Your join request has been cancelled.'];
    }

    /**
     * Retrieve all pending join requests for a group.
     */
    public function getPendingRequests(StudyGroup $group): Collection
    {
        return $group->memberships()
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Approve a pending join request.
     */
    public function approveRequest(StudyGroup $group, StudyGroupMember $member, User $actingUser): array
    {
        if (! $group->isAdmin($actingUser)) {
            return ['status' => 'error', 'message' => 'Unauthorized action. Only group administrators can approve requests.'];
        }

        if ((int) $member->study_group_id !== (int) $group->id) {
            return ['status' => 'error', 'message' => 'Member request does not belong to this group.'];
        }

        if ($member->status === 'active') {
            return ['status' => 'info', 'message' => 'User is already an active member.'];
        }

        if ($group->hasReachedMaxMembers()) {
            return ['status' => 'error', 'message' => 'Cannot approve: group has reached its maximum member limit.'];
        }

        $member->update([
            'status'    => 'active',
            'joined_at' => now(),
        ]);

        return ['status' => 'success', 'message' => "Approved {$member->user->name}'s join request successfully."];
    }

    /**
     * Reject a pending join request.
     */
    public function rejectRequest(StudyGroup $group, StudyGroupMember $member, User $actingUser): array
    {
        if (! $group->isAdmin($actingUser)) {
            return ['status' => 'error', 'message' => 'Unauthorized action. Only group administrators can reject requests.'];
        }

        if ((int) $member->study_group_id !== (int) $group->id) {
            return ['status' => 'error', 'message' => 'Member request does not belong to this group.'];
        }

        $userName = $member->user->name ?? 'User';
        $member->delete();

        return ['status' => 'info', 'message' => "Rejected and removed {$userName}'s join request."];
    }
}
