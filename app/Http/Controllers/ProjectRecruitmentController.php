<?php

namespace App\Http\Controllers;

use App\Models\ProjectRecruitment;
use App\Models\ProjectTeamMember;
use Illuminate\Http\Request;

class ProjectRecruitmentController extends Controller
{
    /**
     * Display a listing of project recruitment posts with search and filter toolbar.
     */
    public function index(Request $request)
    {
        $search      = $request->query('search', '');
        $course      = $request->query('course', '');
        $projectType = $request->query('type', '');
        $status      = $request->query('status', 'all'); // 'all', 'open', 'closed', 'my'

        $query = ProjectRecruitment::with('creator.profile')->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%")
                  ->orWhere('required_skills', 'like', "%{$search}%")
                  ->orWhere('location_name', 'like', "%{$search}%");
            });
        }

        if (!empty($course)) {
            $query->where('course', $course);
        }

        if (!empty($projectType)) {
            $query->where('project_type', $projectType);
        }

        if ($status === 'open') {
            $query->where('recruitment_status', 'open');
        } elseif ($status === 'closed') {
            $query->where('recruitment_status', 'closed');
        } elseif ($status === 'my') {
            $query->where('creator_id', auth()->id());
        }

        $projects = $query->paginate(9)->withQueryString();

        // Get filter counts
        $counts = [
            'all'    => ProjectRecruitment::count(),
            'open'   => ProjectRecruitment::where('recruitment_status', 'open')->count(),
            'closed' => ProjectRecruitment::where('recruitment_status', 'closed')->count(),
            'my'     => ProjectRecruitment::where('creator_id', auth()->id())->count(),
        ];

        // Suggested courses and project types for filter dropdowns
        $courses = [
            'CSE470 - Software Engineering',
            'CSE471 - System Analysis and Design',
            'CSE422 - Artificial Intelligence',
            'CSE370 - Database Systems',
            'CSE321 - Operating Systems',
            'CSE221 - Algorithms',
            'CSE220 - Data Structures',
            'CSE499 - Senior Design / Thesis',
        ];

        $projectTypes = [
            'Course Project',
            'Capstone / Thesis',
            'Hackathon Team',
            'Research Project',
            'Open Source Collaboration',
            'Startup / Product MVP',
        ];

        return view('projects.index', compact(
            'projects',
            'search',
            'course',
            'projectType',
            'status',
            'counts',
            'courses',
            'projectTypes'
        ));
    }

    /**
     * Show the form for creating a new project recruitment post.
     */
    public function create()
    {
        $courses = [
            'CSE470 - Software Engineering',
            'CSE471 - System Analysis and Design',
            'CSE422 - Artificial Intelligence',
            'CSE370 - Database Systems',
            'CSE321 - Operating Systems',
            'CSE221 - Algorithms',
            'CSE220 - Data Structures',
            'CSE499 - Senior Design / Thesis',
        ];

        $projectTypes = [
            'Course Project',
            'Capstone / Thesis',
            'Hackathon Team',
            'Research Project',
            'Open Source Collaboration',
            'Startup / Product MVP',
        ];

        return view('projects.create', compact('courses', 'projectTypes'));
    }

    /**
     * Store a newly created project recruitment post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'required|string',
            'course'             => 'required|string|max:255',
            'project_type'       => 'required|string|max:100',
            'required_members'   => 'required|integer|min:2|max:30',
            'current_members'    => 'nullable|integer|min:1|max:30',
            'required_skills'    => 'nullable|string|max:500',
            'recruitment_status' => 'required|in:open,closed',
            'meeting_date'       => 'nullable|date',
            'meeting_time'       => 'nullable|string|max:50',
            'location_name'      => 'nullable|string|max:255',
            'location_address'   => 'nullable|string|max:255',
            'latitude'           => 'nullable|numeric|between:-90,90',
            'longitude'          => 'nullable|numeric|between:-180,180',
        ]);

        $validated['creator_id']      = auth()->id();
        $validated['current_members'] = 1; // Starts with the creator as the first member

        $project = ProjectRecruitment::create($validated);

        // Automatically assign creator as active creator/member
        ProjectTeamMember::create([
            'project_recruitment_id' => $project->id,
            'user_id'                => auth()->id(),
            'role'                   => 'creator',
            'status'                 => 'active',
            'joined_at'              => now(),
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', "Project recruitment post '{$project->title}' created successfully!");
    }

    /**
     * Display the specified project recruitment post and manage requests.
     */
    public function show(ProjectRecruitment $project)
    {
        $project->loadMissing([
            'creator.profile',
            'activeMembers.user.profile',
            'pendingRequests.user.profile',
        ]);

        $user = auth()->user();
        $isCreator = $project->isCreator($user);
        $userMembership = $project->getMembershipFor($user);

        $googleMapsApiKey = config('services.google_maps.api_key') ?? env('GOOGLE_MAPS_API_KEY', '');

        return view('projects.show', compact(
            'project',
            'isCreator',
            'userMembership',
            'googleMapsApiKey'
        ));
    }

    /**
     * Show the form for editing the specified recruitment post.
     */
    public function edit(ProjectRecruitment $project)
    {
        if (!$project->isCreator(auth()->user())) {
            abort(403, 'Unauthorized action. Only the project creator can edit this recruitment post.');
        }

        $courses = [
            'CSE470 - Software Engineering',
            'CSE471 - System Analysis and Design',
            'CSE422 - Artificial Intelligence',
            'CSE370 - Database Systems',
            'CSE321 - Operating Systems',
            'CSE221 - Algorithms',
            'CSE220 - Data Structures',
            'CSE499 - Senior Design / Thesis',
        ];

        $projectTypes = [
            'Course Project',
            'Capstone / Thesis',
            'Hackathon Team',
            'Research Project',
            'Open Source Collaboration',
            'Startup / Product MVP',
        ];

        return view('projects.edit', compact('project', 'courses', 'projectTypes'));
    }

    /**
     * Update the specified recruitment post in storage.
     */
    public function update(Request $request, ProjectRecruitment $project)
    {
        if (!$project->isCreator(auth()->user())) {
            abort(403, 'Unauthorized action. Only the project creator can update this recruitment post.');
        }

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'required|string',
            'course'             => 'required|string|max:255',
            'project_type'       => 'required|string|max:100',
            'required_members'   => 'required|integer|min:2|max:30',
            'required_skills'    => 'nullable|string|max:500',
            'recruitment_status' => 'required|in:open,closed',
            'meeting_date'       => 'nullable|date',
            'meeting_time'       => 'nullable|string|max:50',
            'location_name'      => 'nullable|string|max:255',
            'location_address'   => 'nullable|string|max:255',
            'latitude'           => 'nullable|numeric|between:-90,90',
            'longitude'          => 'nullable|numeric|between:-180,180',
        ]);

        $project->update($validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', "Project recruitment post '{$project->title}' updated successfully!");
    }

    /**
     * Remove the specified recruitment post from storage.
     */
    public function destroy(ProjectRecruitment $project)
    {
        if (!$project->isCreator(auth()->user())) {
            abort(403, 'Unauthorized action. Only the project creator can delete this recruitment post.');
        }

        $title = $project->title;
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', "Project recruitment post '{$title}' deleted successfully.");
    }

    /**
     * Student requests to join an open project team.
     */
    public function requestJoin(ProjectRecruitment $project)
    {
        $user = auth()->user();

        // 1. Creator cannot request to join their own project
        if ($project->isCreator($user)) {
            return redirect()->back()->with('error', 'You are the creator of this project.');
        }

        // 2. Cannot request if recruitment is closed
        if (!$project->isOpen()) {
            return redirect()->back()->with('error', 'This project is currently closed for recruitment.');
        }

        // 3. Cannot request if project team is full
        if ($project->hasReachedMaxMembers()) {
            return redirect()->back()->with('error', 'This project team has already reached maximum capacity.');
        }

        // 4. Check existing membership/request
        $existing = $project->getMembershipFor($user);
        if ($existing) {
            if ($existing->isActive()) {
                return redirect()->back()->with('info', 'You are already an active member of this project team.');
            }
            if ($existing->isPending()) {
                return redirect()->back()->with('info', 'You already have a pending join request for this project.');
            }
            // If previously rejected, re-apply
            $existing->update([
                'status'    => 'pending',
                'joined_at' => null,
            ]);

            return redirect()->back()->with('success', 'Your request to join the project team has been submitted.');
        }

        ProjectTeamMember::create([
            'project_recruitment_id' => $project->id,
            'user_id'                => $user->id,
            'role'                   => 'member',
            'status'                 => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your request to join the project team has been submitted.');
    }

    /**
     * Student cancels their own pending join request.
     */
    public function cancelRequest(ProjectRecruitment $project)
    {
        $user = auth()->user();

        $membership = $project->teamMemberships()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$membership) {
            return redirect()->back()->with('error', 'No pending join request found.');
        }

        $membership->delete();

        return redirect()->back()->with('success', 'Your join request has been cancelled.');
    }

    /**
     * Project creator approves an applicant's join request.
     */
    public function approveRequest(ProjectRecruitment $project, ProjectTeamMember $member)
    {
        // Creator-only server-side authorization
        if (!$project->isCreator(auth()->user())) {
            abort(403, 'Unauthorized. Only the project creator can approve join requests.');
        }

        if ($member->project_recruitment_id !== $project->id) {
            abort(404);
        }

        // Check if project has reached max capacity
        if ($project->hasReachedMaxMembers()) {
            return redirect()->back()->with('error', 'Cannot approve applicant. The project team has reached maximum capacity.');
        }

        $member->update([
            'status'    => 'active',
            'joined_at' => now(),
        ]);

        $project->syncCurrentMembers();

        $userName = $member->user->name ?? 'Student';

        return redirect()->back()->with('success', "{$userName} has been approved and joined the project team!");
    }

    /**
     * Project creator rejects an applicant's join request.
     */
    public function rejectRequest(ProjectRecruitment $project, ProjectTeamMember $member)
    {
        // Creator-only server-side authorization
        if (!$project->isCreator(auth()->user())) {
            abort(403, 'Unauthorized. Only the project creator can reject join requests.');
        }

        if ($member->project_recruitment_id !== $project->id) {
            abort(404);
        }

        $userName = $member->user->name ?? 'Student';
        $member->delete();

        $project->syncCurrentMembers();

        return redirect()->back()->with('info', "Join request from {$userName} was declined.");
    }
}
