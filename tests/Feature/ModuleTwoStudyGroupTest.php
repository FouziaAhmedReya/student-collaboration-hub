<?php

namespace Tests\Feature;

use App\Models\StudyGroup;
use App\Models\StudyGroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleTwoStudyGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_groups_index()
    {
        $response = $this->get('/groups');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_groups_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/groups');
        $response->assertStatus(200);
        $response->assertSee('Study Group Management');
        $response->assertSee('Create New Group');
    }

    public function test_user_can_create_public_study_group_and_becomes_active_admin()
    {
        $user = User::factory()->create();

        $groupData = [
            'name' => 'Algorithms & Data Structures Study Group',
            'course' => 'CSE221 - Algorithms',
            'max_members' => 8,
            'meeting_date' => '2026-09-01',
            'meeting_time' => '14:30',
            'description' => 'Weekly problem solving sessions on graph algorithms and dynamic programming.',
            'visibility' => 'public',
            'location_name' => 'BRAC University Library',
            'location_address' => 'UB02 Building, 3rd Floor, Mohakhali, Dhaka',
            'latitude' => 23.7806,
            'longitude' => 90.4068,
        ];

        $response = $this->actingAs($user)->post('/groups', $groupData);
        $response->assertRedirect('/groups');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('study_groups', [
            'name' => 'Algorithms & Data Structures Study Group',
            'creator_id' => $user->id,
            'visibility' => 'public',
        ]);

        $group = StudyGroup::where('name', 'Algorithms & Data Structures Study Group')->first();

        // Check creator is automatically active admin member
        $this->assertDatabaseHas('study_group_members', [
            'study_group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->assertTrue($group->isAdmin($user));
        $this->assertTrue($group->isCreator($user));
    }

    public function test_group_creation_validation_rules()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/groups', []);
        $response->assertSessionHasErrors(['name', 'course', 'max_members', 'meeting_date', 'meeting_time', 'description', 'visibility']);
    }

    public function test_group_admin_can_edit_and_update_study_group()
    {
        $user = User::factory()->create();
        $group = StudyGroup::create([
            'creator_id' => $user->id,
            'name' => 'Old Group Name',
            'course' => 'CSE470',
            'max_members' => 6,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Initial description',
            'visibility' => 'public',
        ]);

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'admin',
            'status' => 'active',
        ]);

        $editView = $this->actingAs($user)->get("/groups/{$group->id}/edit");
        $editView->assertStatus(200);
        $editView->assertSee('Edit Study Group');

        $updateResponse = $this->actingAs($user)->put("/groups/{$group->id}", [
            'name' => 'Updated Study Group Name',
            'course' => 'CSE471 - System Analysis and Design',
            'max_members' => 12,
            'meeting_date' => '2026-09-15',
            'meeting_time' => '11:30',
            'description' => 'Updated description text',
            'visibility' => 'private',
            'location_name' => 'UB02 Lounge',
            'location_address' => 'Floor 7',
            'latitude' => 23.7808,
            'longitude' => 90.4072,
        ]);

        $updateResponse->assertRedirect('/groups');
        $this->assertDatabaseHas('study_groups', [
            'id' => $group->id,
            'name' => 'Updated Study Group Name',
            'visibility' => 'private',
            'max_members' => 12,
        ]);
    }

    public function test_unauthorized_user_cannot_edit_or_update_group()
    {
        $creator = User::factory()->create();
        $otherUser = User::factory()->create();

        $group = StudyGroup::create([
            'creator_id' => $creator->id,
            'name' => 'Creator Only Group',
            'course' => 'CSE470',
            'max_members' => 5,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Description',
            'visibility' => 'public',
        ]);

        $response = $this->actingAs($otherUser)->get("/groups/{$group->id}/edit");
        $response->assertStatus(403);

        $updateResponse = $this->actingAs($otherUser)->put("/groups/{$group->id}", [
            'name' => 'Hacked Group Name',
            'course' => 'CSE470',
            'max_members' => 5,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Description',
            'visibility' => 'public',
        ]);
        $updateResponse->assertStatus(403);
    }

    public function test_group_admin_can_delete_group_with_cascading_memberships()
    {
        $admin = User::factory()->create();
        $member = User::factory()->create();

        $group = StudyGroup::create([
            'creator_id' => $admin->id,
            'name' => 'To Be Deleted',
            'course' => 'CSE110',
            'max_members' => 5,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Description',
            'visibility' => 'public',
        ]);

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $admin->id,
            'role' => 'admin',
            'status' => 'active',
        ]);

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $member->id,
            'role' => 'member',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->delete("/groups/{$group->id}");
        $response->assertRedirect('/groups');

        $this->assertDatabaseMissing('study_groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('study_group_members', ['study_group_id' => $group->id]);
    }

    public function test_user_can_join_public_group_directly()
    {
        $creator = User::factory()->create();
        $student = User::factory()->create();

        $group = StudyGroup::create([
            'creator_id' => $creator->id,
            'name' => 'Open Math Circle',
            'course' => 'MAT110',
            'max_members' => 10,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Calculus discussions',
            'visibility' => 'public',
        ]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/join");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('study_group_members', [
            'study_group_id' => $group->id,
            'user_id' => $student->id,
            'status' => 'active',
            'role' => 'member',
        ]);
    }

    public function test_user_joins_private_group_as_pending_approval()
    {
        $creator = User::factory()->create();
        $student = User::factory()->create();

        $group = StudyGroup::create([
            'creator_id' => $creator->id,
            'name' => 'Private Research Group',
            'course' => 'CSE499',
            'max_members' => 5,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Thesis preparation',
            'visibility' => 'private',
        ]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/join");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('study_group_members', [
            'study_group_id' => $group->id,
            'user_id' => $student->id,
            'status' => 'pending',
            'role' => 'member',
        ]);
    }

    public function test_cannot_join_group_when_max_capacity_reached()
    {
        $creator = User::factory()->create();
        $student = User::factory()->create();

        $group = StudyGroup::create([
            'creator_id' => $creator->id,
            'name' => 'Full Group',
            'course' => 'CSE321',
            'max_members' => 1,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Operating systems',
            'visibility' => 'public',
        ]);

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $creator->id,
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student)->post("/groups/{$group->id}/join");
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('study_group_members', [
            'study_group_id' => $group->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_member_can_leave_group()
    {
        $creator = User::factory()->create();
        $member = User::factory()->create();

        $group = StudyGroup::create([
            'creator_id' => $creator->id,
            'name' => 'Leaving Group',
            'course' => 'PHY111',
            'max_members' => 10,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Physics',
            'visibility' => 'public',
        ]);

        $membership = StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $member->id,
            'role' => 'member',
            'status' => 'active',
        ]);

        $response = $this->actingAs($member)->post("/groups/{$group->id}/leave");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('study_group_members', ['id' => $membership->id]);
    }

    public function test_admin_can_view_and_manage_group_members()
    {
        $admin = User::factory()->create();
        $member = User::factory()->create(['name' => 'Charlie Bob']);

        $group = StudyGroup::create([
            'creator_id' => $admin->id,
            'name' => 'Member Management Group',
            'course' => 'CSE471',
            'max_members' => 10,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Testing members',
            'visibility' => 'public',
        ]);

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $admin->id,
            'role' => 'admin',
            'status' => 'active',
        ]);

        $membership = StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $member->id,
            'role' => 'member',
            'status' => 'pending',
        ]);

        $view = $this->actingAs($admin)->get("/groups/{$group->id}/members");
        $view->assertStatus(200);
        $view->assertSee('Group Members');
        $view->assertSee('Charlie Bob');

        // Activate pending member
        $activateResponse = $this->actingAs($admin)->patch("/groups/{$group->id}/members/{$membership->id}/status", [
            'status' => 'active',
        ]);
        $activateResponse->assertRedirect();
        $this->assertDatabaseHas('study_group_members', [
            'id' => $membership->id,
            'status' => 'active',
        ]);

        // Promote to Admin
        $roleResponse = $this->actingAs($admin)->patch("/groups/{$group->id}/members/{$membership->id}/role", [
            'role' => 'admin',
        ]);
        $roleResponse->assertRedirect();
        $this->assertDatabaseHas('study_group_members', [
            'id' => $membership->id,
            'role' => 'admin',
        ]);

        // Remove member
        $deleteResponse = $this->actingAs($admin)->delete("/groups/{$group->id}/members/{$membership->id}");
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('study_group_members', ['id' => $membership->id]);
    }

    public function test_admin_can_invite_existing_user()
    {
        $admin = User::factory()->create();
        $invitedUser = User::factory()->create(['name' => 'Alice Invited']);

        $group = StudyGroup::create([
            'creator_id' => $admin->id,
            'name' => 'Invite Testing Group',
            'course' => 'CSE471',
            'max_members' => 10,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Invite testing',
            'visibility' => 'private',
        ]);

        StudyGroupMember::create([
            'study_group_id' => $group->id,
            'user_id' => $admin->id,
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post("/groups/{$group->id}/members/invite", [
            'user_id' => $invitedUser->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('study_group_members', [
            'study_group_id' => $group->id,
            'user_id' => $invitedUser->id,
            'status' => 'pending',
            'role' => 'member',
        ]);
    }

    public function test_search_and_filter_groups()
    {
        $user = User::factory()->create();

        $group1 = StudyGroup::create([
            'creator_id' => $user->id,
            'name' => 'Artificial Intelligence Circle',
            'course' => 'CSE422',
            'max_members' => 10,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Machine learning and deep learning',
            'visibility' => 'public',
        ]);

        $group2 = StudyGroup::create([
            'creator_id' => $user->id,
            'name' => 'Software Architecture Hub',
            'course' => 'CSE470',
            'max_members' => 10,
            'meeting_date' => '2026-09-10',
            'meeting_time' => '10:00',
            'description' => 'Design patterns',
            'visibility' => 'private',
        ]);

        // Search test
        $response = $this->actingAs($user)->get('/groups?search=Artificial');
        $response->assertStatus(200);
        $response->assertSee('Artificial Intelligence Circle');
        $response->assertDontSee('Software Architecture Hub');

        // Filter private test
        $responsePrivate = $this->actingAs($user)->get('/groups?filter=private');
        $responsePrivate->assertStatus(200);
        $responsePrivate->assertSee('Software Architecture Hub');
        $responsePrivate->assertDontSee('Artificial Intelligence Circle');
    }
}
