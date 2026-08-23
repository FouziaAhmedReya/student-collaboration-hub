<?php

namespace Tests\Feature;

use App\Models\ProjectRecruitment;
use App\Models\ProjectTeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRecruitmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_projects_index(): void
    {
        $response = $this->get('/projects');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_browse_projects(): void
    {
        $user = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $user->id,
            'title'              => 'Distributed Systems Simulator',
            'description'        => 'Building a Raft consensus simulation in Go.',
            'course'             => 'CSE471 - System Analysis and Design',
            'project_type'       => 'Course Project',
            'required_members'   => 4,
            'current_members'    => 1,
            'required_skills'    => 'Go, Docker, gRPC',
            'recruitment_status' => 'open',
        ]);

        $response = $this->actingAs($user)->get('/projects');
        $response->assertStatus(200);
        $response->assertSee('Project Team Finder');
        $response->assertSee('Distributed Systems Simulator');
        $response->assertSee('Go');
    }

    public function test_student_can_create_project_recruitment_post(): void
    {
        $user = User::factory()->create();

        $data = [
            'title'              => 'Smart Campus Attendance App',
            'description'        => 'Mobile application using BLE beacons for automated attendance.',
            'course'             => 'CSE470 - Software Engineering',
            'project_type'       => 'Capstone / Thesis',
            'required_members'   => 4,
            'current_members'    => 1,
            'required_skills'    => 'Flutter, Firebase, Dart',
            'recruitment_status' => 'open',
            'meeting_date'       => '2026-09-05',
            'meeting_time'       => '16:00',
            'location_name'      => 'UB02 Room 704',
            'location_address'   => 'Mohakhali, Dhaka',
            'latitude'           => 23.7806,
            'longitude'          => 90.4068,
        ];

        $response = $this->actingAs($user)->post('/projects/recruitment', $data);

        $this->assertDatabaseHas('project_recruitments', [
            'title'      => 'Smart Campus Attendance App',
            'creator_id' => $user->id,
            'course'     => 'CSE470 - Software Engineering',
        ]);

        $project = ProjectRecruitment::where('title', 'Smart Campus Attendance App')->first();
        $response->assertRedirect("/projects/recruitment/{$project->id}");

        // Verify creator is automatically an active member with creator role
        $this->assertDatabaseHas('project_team_members', [
            'project_recruitment_id' => $project->id,
            'user_id'                => $user->id,
            'role'                   => 'creator',
            'status'                 => 'active',
        ]);
    }

    public function test_invalid_project_data_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects/recruitment', []);
        $response->assertSessionHasErrors(['title', 'description', 'course', 'project_type', 'required_members', 'recruitment_status']);
    }

    public function test_student_can_view_project_details(): void
    {
        $creator = User::factory()->create();
        $viewer = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'AI Chatbot for BRACU Students',
            'description'        => 'Conversational assistant answering student queries on course registration.',
            'course'             => 'CSE422 - Artificial Intelligence',
            'project_type'       => 'Research Project',
            'required_members'   => 3,
            'current_members'    => 1,
            'required_skills'    => 'Python, PyTorch, FastAPI',
            'recruitment_status' => 'open',
            'location_name'      => 'UB02 Computer Lab',
            'location_address'   => 'Mohakhali, Dhaka',
        ]);

        ProjectTeamMember::create([
            'project_recruitment_id' => $project->id,
            'user_id'                => $creator->id,
            'role'                   => 'creator',
            'status'                 => 'active',
            'joined_at'              => now(),
        ]);

        $response = $this->actingAs($viewer)->get("/projects/recruitment/{$project->id}");
        $response->assertStatus(200);
        $response->assertSee('AI Chatbot for BRACU Students');
        $response->assertSee('Python');
        $response->assertSee('PyTorch');
        $response->assertSee($creator->name);
        $response->assertSee('Request to Join');
    }

    public function test_creator_can_edit_own_project(): void
    {
        $creator = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Old Project Title',
            'description'        => 'Initial description',
            'course'             => 'CSE370 - Database Systems',
            'project_type'       => 'Course Project',
            'required_members'   => 3,
            'current_members'    => 1,
            'recruitment_status' => 'open',
        ]);

        $editView = $this->actingAs($creator)->get("/projects/recruitment/{$project->id}/edit");
        $editView->assertStatus(200);
        $editView->assertSee('Edit Project Recruitment Post');

        $response = $this->actingAs($creator)->put("/projects/recruitment/{$project->id}", [
            'title'              => 'Updated Title: High Performance DBMS',
            'description'        => 'Updated project goals.',
            'course'             => 'CSE370 - Database Systems',
            'project_type'       => 'Open Source Collaboration',
            'required_members'   => 5,
            'required_skills'    => 'C++, SQL, Redis',
            'recruitment_status' => 'closed',
        ]);

        $response->assertRedirect("/projects/recruitment/{$project->id}");
        $this->assertDatabaseHas('project_recruitments', [
            'id'                 => $project->id,
            'title'              => 'Updated Title: High Performance DBMS',
            'recruitment_status' => 'closed',
        ]);
    }

    public function test_creator_can_delete_own_project(): void
    {
        $creator = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Project To Delete',
            'description'        => 'Will be removed',
            'course'             => 'CSE110',
            'project_type'       => 'Course Project',
            'required_members'   => 3,
            'current_members'    => 1,
            'recruitment_status' => 'open',
        ]);

        $response = $this->actingAs($creator)->delete("/projects/recruitment/{$project->id}");
        $response->assertRedirect('/projects');
        $this->assertDatabaseMissing('project_recruitments', ['id' => $project->id]);
    }

    public function test_non_creator_cannot_edit_or_update_project(): void
    {
        $creator = User::factory()->create();
        $otherStudent = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Protected Project',
            'description'        => 'Only creator can modify',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 4,
            'current_members'    => 1,
            'recruitment_status' => 'open',
        ]);

        // Attempt edit
        $responseEdit = $this->actingAs($otherStudent)->get("/projects/recruitment/{$project->id}/edit");
        $responseEdit->assertStatus(403);

        // Attempt update
        $responseUpdate = $this->actingAs($otherStudent)->put("/projects/recruitment/{$project->id}", [
            'title'              => 'Hacked Project Title',
            'description'        => 'Malicious update',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 4,
            'recruitment_status' => 'open',
        ]);
        $responseUpdate->assertStatus(403);
    }

    public function test_non_creator_cannot_delete_project(): void
    {
        $creator = User::factory()->create();
        $otherStudent = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Protected Project Deletion',
            'description'        => 'Cannot be deleted by others',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 4,
            'current_members'    => 1,
            'recruitment_status' => 'open',
        ]);

        $response = $this->actingAs($otherStudent)->delete("/projects/recruitment/{$project->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('project_recruitments', ['id' => $project->id]);
    }

    public function test_student_can_request_to_join_and_cancel_request(): void
    {
        $creator = User::factory()->create();
        $student = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Autonomous Drone Navigation',
            'description'        => 'ROS2 and Computer Vision',
            'course'             => 'CSE422',
            'project_type'       => 'Research Project',
            'required_members'   => 3,
            'current_members'    => 1,
            'recruitment_status' => 'open',
        ]);

        ProjectTeamMember::create([
            'project_recruitment_id' => $project->id,
            'user_id'                => $creator->id,
            'role'                   => 'creator',
            'status'                 => 'active',
            'joined_at'              => now(),
        ]);

        // Student sends join request
        $response = $this->actingAs($student)->post("/projects/recruitment/{$project->id}/request");
        $response->assertRedirect();
        $this->assertDatabaseHas('project_team_members', [
            'project_recruitment_id' => $project->id,
            'user_id'                => $student->id,
            'status'                 => 'pending',
            'role'                   => 'member',
        ]);

        // Detail page shows Pending Approval
        $showPage = $this->actingAs($student)->get("/projects/recruitment/{$project->id}");
        $showPage->assertSee('Pending Approval');
        $showPage->assertSee('Cancel Request');

        // Student cancels their pending request
        $cancelResponse = $this->actingAs($student)->delete("/projects/recruitment/{$project->id}/request");
        $cancelResponse->assertRedirect();
        $this->assertDatabaseMissing('project_team_members', [
            'project_recruitment_id' => $project->id,
            'user_id'                => $student->id,
        ]);
    }

    public function test_creator_cannot_request_to_join_own_project(): void
    {
        $creator = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'My Project',
            'description'        => 'Desc',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 3,
            'current_members'    => 1,
            'recruitment_status' => 'open',
        ]);

        $response = $this->actingAs($creator)->post("/projects/recruitment/{$project->id}/request");
        $response->assertSessionHas('error');
    }

    public function test_cannot_request_to_join_closed_or_full_project(): void
    {
        $creator = User::factory()->create();
        $student = User::factory()->create();

        // 1. Closed Project
        $closedProject = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Closed Project',
            'description'        => 'Desc',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 3,
            'current_members'    => 1,
            'recruitment_status' => 'closed',
        ]);

        $resClosed = $this->actingAs($student)->post("/projects/recruitment/{$closedProject->id}/request");
        $resClosed->assertSessionHas('error');

        // 2. Full Project
        $fullProject = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Full Project',
            'description'        => 'Desc',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 2,
            'current_members'    => 2,
            'recruitment_status' => 'open',
        ]);

        $resFull = $this->actingAs($student)->post("/projects/recruitment/{$fullProject->id}/request");
        $resFull->assertSessionHas('error');
    }

    public function test_creator_can_approve_and_reject_applicant_requests(): void
    {
        $creator = User::factory()->create(['name' => 'Creator Student']);
        $applicant1 = User::factory()->create(['name' => 'Applicant Alice']);
        $applicant2 = User::factory()->create(['name' => 'Applicant Bob']);

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Fintech Mobile Wallet',
            'description'        => 'Digital payment app with NFC support.',
            'course'             => 'CSE470',
            'project_type'       => 'Course Project',
            'required_members'   => 3,
            'current_members'    => 1,
            'recruitment_status' => 'open',
        ]);

        ProjectTeamMember::create([
            'project_recruitment_id' => $project->id,
            'user_id'                => $creator->id,
            'role'                   => 'creator',
            'status'                 => 'active',
            'joined_at'              => now(),
        ]);

        $req1 = ProjectTeamMember::create([
            'project_recruitment_id' => $project->id,
            'user_id'                => $applicant1->id,
            'role'                   => 'member',
            'status'                 => 'pending',
        ]);

        $req2 = ProjectTeamMember::create([
            'project_recruitment_id' => $project->id,
            'user_id'                => $applicant2->id,
            'role'                   => 'member',
            'status'                 => 'pending',
        ]);

        // Creator views team requests section
        $showPage = $this->actingAs($creator)->get("/projects/recruitment/{$project->id}");
        $showPage->assertSee('Team Requests');
        $showPage->assertSee('Applicant Alice');
        $showPage->assertSee('Applicant Bob');

        // Creator approves Applicant 1
        $appResponse = $this->actingAs($creator)->patch("/projects/recruitment/{$project->id}/requests/{$req1->id}/approve");
        $appResponse->assertRedirect();
        $this->assertDatabaseHas('project_team_members', [
            'id'     => $req1->id,
            'status' => 'active',
        ]);

        $project->refresh();
        $this->assertEquals(2, $project->current_members);

        // Applicant 1 now sees Already Joined
        $alicePage = $this->actingAs($applicant1)->get("/projects/recruitment/{$project->id}");
        $alicePage->assertSee('Already Joined');

        // Creator rejects Applicant 2
        $rejResponse = $this->actingAs($creator)->patch("/projects/recruitment/{$project->id}/requests/{$req2->id}/reject");
        $rejResponse->assertRedirect();
        $this->assertDatabaseMissing('project_team_members', [
            'id' => $req2->id,
        ]);

        $project->refresh();
        $this->assertEquals(2, $project->current_members);
    }

    public function test_non_creator_cannot_approve_or_reject_requests(): void
    {
        $creator = User::factory()->create();
        $hacker = User::factory()->create();
        $applicant = User::factory()->create();

        $project = ProjectRecruitment::create([
            'creator_id'         => $creator->id,
            'title'              => 'Secure Project',
            'description'        => 'Desc',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 4,
            'current_members'    => 1,
            'recruitment_status' => 'open',
        ]);

        $request = ProjectTeamMember::create([
            'project_recruitment_id' => $project->id,
            'user_id'                => $applicant->id,
            'role'                   => 'member',
            'status'                 => 'pending',
        ]);

        // Hacker attempts approval
        $hackerApprove = $this->actingAs($hacker)->patch("/projects/recruitment/{$project->id}/requests/{$request->id}/approve");
        $hackerApprove->assertStatus(403);

        // Hacker attempts rejection
        $hackerReject = $this->actingAs($hacker)->patch("/projects/recruitment/{$project->id}/requests/{$request->id}/reject");
        $hackerReject->assertStatus(403);
    }

    public function test_search_and_filtering(): void
    {
        $user = User::factory()->create();

        $p1 = ProjectRecruitment::create([
            'creator_id'         => $user->id,
            'title'              => 'Quantum Computing Simulator',
            'description'        => 'Qiskit Python project',
            'course'             => 'CSE422 - Artificial Intelligence',
            'project_type'       => 'Research Project',
            'required_members'   => 3,
            'current_members'    => 1,
            'required_skills'    => 'Python, Qiskit',
            'recruitment_status' => 'open',
        ]);

        $p2 = ProjectRecruitment::create([
            'creator_id'         => $user->id,
            'title'              => 'Web Security Penetration Testing Lab',
            'description'        => 'OWASP security tests',
            'course'             => 'CSE470 - Software Engineering',
            'project_type'       => 'Course Project',
            'required_members'   => 4,
            'current_members'    => 4,
            'required_skills'    => 'Kali Linux, BurpSuite',
            'recruitment_status' => 'closed',
        ]);

        // Search test
        $resSearch = $this->actingAs($user)->get('/projects?search=Quantum');
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Quantum Computing Simulator');
        $resSearch->assertDontSee('Web Security Penetration Testing Lab');

        // Status filter test (closed)
        $resClosed = $this->actingAs($user)->get('/projects?status=closed');
        $resClosed->assertStatus(200);
        $resClosed->assertSee('Web Security Penetration Testing Lab');
        $resClosed->assertDontSee('Quantum Computing Simulator');
    }

    public function test_map_and_location_rendering_with_openfreemap_and_fallback(): void
    {
        $user = User::factory()->create();

        // 1. Project with coordinates
        $projectWithCoords = ProjectRecruitment::create([
            'creator_id'         => $user->id,
            'title'              => 'Location With Coordinates',
            'description'        => 'Testing maps with coordinates',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 4,
            'current_members'    => 1,
            'location_name'      => 'UB02 Room 704',
            'location_address'   => 'Mohakhali, Dhaka',
            'latitude'           => 23.7806,
            'longitude'          => 90.4068,
            'recruitment_status' => 'open',
        ]);

        $responseCoords = $this->actingAs($user)->get("/projects/recruitment/{$projectWithCoords->id}");
        $responseCoords->assertStatus(200);
        $responseCoords->assertSee('UB02 Room 704');
        $responseCoords->assertSee('Open in OpenStreetMap');
        $responseCoords->assertSee('projectLeafletMap');

        // 2. Project without coordinates (graceful fallback)
        $projectNoCoords = ProjectRecruitment::create([
            'creator_id'         => $user->id,
            'title'              => 'Location Without Coordinates',
            'description'        => 'Testing maps fallback without coordinates',
            'course'             => 'CSE471',
            'project_type'       => 'Course Project',
            'required_members'   => 4,
            'current_members'    => 1,
            'location_name'      => 'Virtual Space',
            'location_address'   => 'Online Room',
            'latitude'           => null,
            'longitude'          => null,
            'recruitment_status' => 'open',
        ]);

        $responseFallback = $this->actingAs($user)->get("/projects/recruitment/{$projectNoCoords->id}");
        $responseFallback->assertStatus(200);
        $responseFallback->assertSee('Virtual Space');
        $responseFallback->assertSee('Map location unavailable');
    }
}
