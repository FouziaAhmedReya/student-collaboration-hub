<?php

namespace App\Http\Controllers\Modules\Rayhan;

use App\Http\Controllers\Controller;
use App\Models\DepartmentInterest;
use App\Models\Interest;
use App\Models\PortfolioLink;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\StudentProject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileSkillController extends Controller
{
    /**
     * Get or initialize the active user.
     */
    protected function getActiveUser(): User
    {
        if (Auth::check()) {
            return Auth::user();
        }

        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Rayhan Student',
                'email' => 'rayhan.student@example.com',
                'password' => bcrypt('password123'),
            ]);
        }

        Auth::login($user);
        return $user;
    }

    /**
     * Get or create the profile for the active user.
     */
    protected function getActiveProfile(): Profile
    {
        $user = $this->getActiveUser();

        return Profile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'department' => 'Computer Science & Engineering',
                'semester' => 'Fall 2026',
                'university' => 'University of Dhaka',
                'bio' => 'Passionate software engineering student exploring full-stack web development, system architecture, and AI-driven applications.',
                'about_me' => 'Passionate software engineering student exploring full-stack web development, system architecture, and AI-driven applications.',
                'phone' => '+880 1712-345678',
                'preferred_location_name' => 'Central Campus Library, Study Hall A',
                'preferred_location_address' => 'Central Campus Library, 2nd Floor, Dhaka',
                'latitude' => 23.777176,
                'longitude' => 90.399452,
            ]
        );
    }

    /**
     * Display the Student Profile dashboard.
     */
    public function index(): View
    {
        $user = $this->getActiveUser();
        $profile = $this->getActiveProfile();

        $profile->load([
            'skills' => fn ($query) => $query->latest(),
            'interests' => fn ($query) => $query->latest(),
            'studentProjects' => fn ($query) => $query->latest(),
            'portfolioLinks' => fn ($query) => $query->latest(),
        ]);

        $completionPercentage = $profile->completion_percentage;
        $completionDetails = $profile->completion_details;

        return view('modules.rayhan.profile-skills.index', [
            'user' => $user,
            'profile' => $profile,
            'completionPercentage' => $completionPercentage,
            'completionDetails' => $completionDetails,
        ]);
    }

    /**
     * Show form to edit personal/academic information and location.
     */
    public function edit(): View
    {
        $user = $this->getActiveUser();
        $profile = $this->getActiveProfile();

        $departments = [
            'Computer Science & Engineering',
            'Software Engineering',
            'Information Technology',
            'Electrical & Electronic Engineering',
            'Data Science & Analytics',
            'Business Administration',
        ];

        return view('modules.rayhan.profile-skills.edit', [
            'user' => $user,
            'profile' => $profile,
            'departments' => $departments,
        ]);
    }

    /**
     * Update personal and profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $this->getActiveUser();
        $profile = $this->getActiveProfile();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'department' => ['required', 'string', 'max:120'],
            'semester' => ['required', 'string', 'max:50'],
            'university' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:1500'],
            'about_me' => ['nullable', 'string', 'max:1500'],
            'preferred_location_name' => ['nullable', 'string', 'max:150'],
            'preferred_location_address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $bio = $validated['bio'] ?? $validated['about_me'] ?? null;
        $aboutMe = $validated['about_me'] ?? $validated['bio'] ?? null;

        DB::transaction(function () use ($user, $profile, $validated, $bio, $aboutMe) {
            $user->update([
                'name' => $validated['name'],
            ]);

            $profile->update([
                'department' => $validated['department'],
                'semester' => $validated['semester'],
                'university' => $validated['university'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'bio' => $bio,
                'about_me' => $aboutMe,
                'preferred_location_name' => $validated['preferred_location_name'] ?? null,
                'preferred_location_address' => $validated['preferred_location_address'] ?? null,
                'latitude' => $validated['latitude'] !== null ? (float) $validated['latitude'] : null,
                'longitude' => $validated['longitude'] !== null ? (float) $validated['longitude'] : null,
            ]);
        });

        return redirect()->route('profile.index')
            ->with('success', 'Profile information updated successfully.');
    }

    /* -------------------------------------------------------------------------- */
    /*                              SKILLS CRUD                                   */
    /* -------------------------------------------------------------------------- */

    public function storeSkill(Request $request): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'proficiency' => ['nullable', 'integer', 'min:0', 'max:100'],
            'proficiency_level' => ['nullable', 'in:Beginner,Intermediate,Advanced,Expert'],
            'category' => ['nullable', 'string', 'max:60'],
        ]);

        // Harmonize proficiency integer and level string
        if (isset($validated['proficiency']) && !isset($validated['proficiency_level'])) {
            $validated['proficiency_level'] = Skill::proficiencyLevelFromInt((int) $validated['proficiency']);
        } elseif (isset($validated['proficiency_level']) && !isset($validated['proficiency'])) {
            $validated['proficiency'] = Skill::intFromProficiencyLevel($validated['proficiency_level']);
        } elseif (!isset($validated['proficiency']) && !isset($validated['proficiency_level'])) {
            $validated['proficiency_level'] = 'Intermediate';
            $validated['proficiency'] = 50;
        }

        $profile->skills()->create($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Skill added successfully.');
    }

    public function updateSkill(Request $request, Skill $skill): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        if ($skill->profile_id !== $profile->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'proficiency' => ['nullable', 'integer', 'min:0', 'max:100'],
            'proficiency_level' => ['nullable', 'in:Beginner,Intermediate,Advanced,Expert'],
            'category' => ['nullable', 'string', 'max:60'],
        ]);

        if (isset($validated['proficiency']) && !isset($validated['proficiency_level'])) {
            $validated['proficiency_level'] = Skill::proficiencyLevelFromInt((int) $validated['proficiency']);
        } elseif (isset($validated['proficiency_level']) && !isset($validated['proficiency'])) {
            $validated['proficiency'] = Skill::intFromProficiencyLevel($validated['proficiency_level']);
        }

        $skill->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Skill updated successfully.');
    }

    public function destroySkill(Skill $skill): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        if ($skill->profile_id !== $profile->id) {
            abort(403, 'Unauthorized action.');
        }

        $skill->delete();

        return redirect()->route('profile.index')
            ->with('success', 'Skill removed successfully.');
    }

    /* -------------------------------------------------------------------------- */
    /*                             INTERESTS CRUD                                 */
    /* -------------------------------------------------------------------------- */

    public function storeInterest(Request $request): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:60'],
        ]);

        $profile->interests()->create($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Interest added successfully.');
    }

    public function updateInterest(Request $request, Interest $interest): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        if ($interest->profile_id !== $profile->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:60'],
        ]);

        $interest->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Interest updated successfully.');
    }

    public function destroyInterest(Interest $interest): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        if ($interest->profile_id !== $profile->id) {
            abort(403, 'Unauthorized action.');
        }

        $interest->delete();

        return redirect()->route('profile.index')
            ->with('success', 'Interest removed successfully.');
    }

    /**
     * Get interest suggestions by department.
     */
    public function interestSuggestions(Request $request): JsonResponse
    {
        $profile = $this->getActiveProfile();
        $department = $request->query('department', $profile->department);

        $query = DepartmentInterest::query();
        if ($department) {
            $query->where('department', $department);
        }

        $suggestions = $query->pluck('name');

        if ($suggestions->isEmpty()) {
            $suggestions = DepartmentInterest::pluck('name')->unique()->values();
        }

        return response()->json([
            'department' => $department,
            'suggestions' => $suggestions,
        ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                         COMPLETED PROJECTS CRUD                            */
    /* -------------------------------------------------------------------------- */

    public function storeProject(Request $request): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1500'],
            'technologies' => ['nullable', 'string', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'completed_date' => ['nullable', 'string', 'max:50'],
        ]);

        $profile->studentProjects()->create($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Completed project added successfully.');
    }

    public function updateProject(Request $request, StudentProject $project): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        if ($project->profile_id !== $profile->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1500'],
            'technologies' => ['nullable', 'string', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'completed_date' => ['nullable', 'string', 'max:50'],
        ]);

        $project->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroyProject(StudentProject $project): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        if ($project->profile_id !== $profile->id) {
            abort(403, 'Unauthorized action.');
        }

        $project->delete();

        return redirect()->route('profile.index')
            ->with('success', 'Project removed successfully.');
    }

    /* -------------------------------------------------------------------------- */
    /*                          PORTFOLIO LINKS CRUD                              */
    /* -------------------------------------------------------------------------- */

    public function storePortfolioLink(Request $request): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:60'],
            'platform' => ['nullable', 'string', 'max:60'],
            'url' => ['required', 'url', 'max:255'],
        ]);

        // Default title to platform or vice versa if one is missing
        if (empty($validated['title']) && !empty($validated['platform'])) {
            $validated['title'] = $validated['platform'];
        } elseif (!empty($validated['title']) && empty($validated['platform'])) {
            $validated['platform'] = $validated['title'];
        } elseif (empty($validated['title']) && empty($validated['platform'])) {
            $validated['title'] = 'Portfolio';
            $validated['platform'] = 'Website';
        }

        $profile->portfolioLinks()->create($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Portfolio link added successfully.');
    }

    public function updatePortfolioLink(Request $request, PortfolioLink $link): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        if ($link->profile_id !== $profile->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:60'],
            'platform' => ['nullable', 'string', 'max:60'],
            'url' => ['required', 'url', 'max:255'],
        ]);

        if (empty($validated['title']) && !empty($validated['platform'])) {
            $validated['title'] = $validated['platform'];
        } elseif (!empty($validated['title']) && empty($validated['platform'])) {
            $validated['platform'] = $validated['title'];
        }

        $link->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Portfolio link updated successfully.');
    }

    public function destroyPortfolioLink(PortfolioLink $link): RedirectResponse
    {
        $profile = $this->getActiveProfile();

        if ($link->profile_id !== $profile->id) {
            abort(403, 'Unauthorized action.');
        }

        $link->delete();

        return redirect()->route('profile.index')
            ->with('success', 'Portfolio link removed successfully.');
    }
}
