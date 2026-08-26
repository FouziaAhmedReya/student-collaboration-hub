<?php

namespace App\Http\Controllers\Modules\Tuli;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\User;
use App\Services\Tuli\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class JwtAuthController extends Controller
{
    /**
     * Show JWT Login Form.
     */
    public function showLoginForm(Request $request)
    {
        $user = JwtService::getUserFromRequest($request);
        if ($user) {
            return redirect()->route('project-ideas.index');
        }
        return view('modules.tuli.auth.login');
    }

    /**
     * Authenticate user credentials and return JWT token.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', strtolower($validated['email']))->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Invalid email or password credentials.'], 401);
            }
            return back()->withErrors(['email' => 'Invalid email or password credentials.'])->withInput();
        }

        // Login user in Laravel Auth session as well
        Auth::login($user);

        // Generate JWT Token
        $jwtToken = JwtService::generateToken($user);

        // Store JWT token in session & cookie
        session(['jwt_token' => $jwtToken]);
        cookie()->queue('jwt_token', $jwtToken, 60 * 24 * 7);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Login successful',
                'token' => $jwtToken,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ], 200);
        }

        return redirect()->route('project-ideas.index')
            ->with('success', "Welcome back, {$user->name}!")
            ->withCookie(cookie('jwt_token', $jwtToken, 60 * 24 * 7));
    }

    /**
     * Show JWT Registration Form.
     */
    public function showRegisterForm(Request $request)
    {
        $user = JwtService::getUserFromRequest($request);
        if ($user) {
            return redirect()->route('project-ideas.index');
        }
        return view('modules.tuli.auth.register');
    }

    /**
     * Register a new student and issue signed JWT token.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'department' => 'nullable|string|max:255',
            'skills' => 'nullable|string',
            'interests' => 'nullable|string',
            'bio' => 'nullable|string',
            'about_me' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        $profile = Profile::create([
            'user_id' => $user->id,
            'department' => $validated['department'] ?? 'Computer Science',
            'about_me' => $validated['about_me'] ?? ($validated['bio'] ?? null),
            'bio' => $validated['bio'] ?? ($validated['about_me'] ?? null),
        ]);

        // Save skills if provided
        if (!empty($validated['skills'])) {
            $skillNames = array_filter(array_map('trim', explode(',', $validated['skills'])));
            foreach ($skillNames as $skName) {
                Skill::create([
                    'profile_id' => $profile->id,
                    'name' => $skName,
                    'proficiency' => 80,
                    'proficiency_level' => 'Advanced',
                ]);
            }
        }

        // Save interests if provided
        if (!empty($validated['interests'])) {
            $interestNames = array_filter(array_map('trim', explode(',', $validated['interests'])));
            foreach ($interestNames as $intName) {
                Interest::create([
                    'profile_id' => $profile->id,
                    'name' => $intName,
                    'category' => 'General',
                ]);
            }
        }

        Auth::login($user);

        // Generate JWT Token
        $jwtToken = JwtService::generateToken($user);

        session(['jwt_token' => $jwtToken]);
        cookie()->queue('jwt_token', $jwtToken, 60 * 24 * 7);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Registration successful',
                'token' => $jwtToken,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'department' => $profile->department,
                ],
            ], 201);
        }

        return redirect()->route('project-ideas.index')
            ->with('success', "Registration successful! Welcome, {$user->name}.")
            ->withCookie(cookie('jwt_token', $jwtToken, 60 * 24 * 7));
    }

    /**
     * Show Edit Profile Form (JWT Verified).
     */
    public function showProfileForm(Request $request)
    {
        $user = JwtService::getUserFromRequest($request);
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your profile.']);
        }

        $user->load(['profile.skills', 'profile.interests', 'profile.studentProjects', 'profile.portfolioLinks']);
        $profile = $user->profile;

        $skillsStr = ($profile && $profile->skills) ? $profile->skills->pluck('name')->implode(', ') : '';
        $interestsStr = ($profile && $profile->interests) ? $profile->interests->pluck('name')->implode(', ') : '';
        $projectsStr = ($profile && $profile->studentProjects) ? $profile->studentProjects->pluck('title')->implode(', ') : '';
        $portfolioStr = ($profile && $profile->portfolioLinks) ? $profile->portfolioLinks->pluck('url')->implode(', ') : '';

        return view('modules.tuli.auth.profile', compact('user', 'profile', 'skillsStr', 'interestsStr', 'projectsStr', 'portfolioStr'));
    }

    /**
     * Update Student Profile (JWT Verified).
     */
    public function updateProfile(Request $request)
    {
        $user = JwtService::getUserFromRequest($request);
        if (!$user) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Unauthenticated JWT token.'], 401);
            }
            return redirect()->route('login')->withErrors(['email' => 'Please login to access your profile.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'location_name' => 'nullable|string|max:255',
            'skills' => 'nullable|string',
            'interests' => 'nullable|string',
            'projects' => 'nullable|string',
            'portfolio' => 'nullable|string',
            'bio' => 'nullable|string',
            'about_me' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $validated['name'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $profile->department = $validated['department'] ?? $profile->department;
        if (array_key_exists('location_name', $validated)) {
            $profile->location_name = $validated['location_name'];
        }
        $profile->bio = $validated['bio'] ?? ($validated['about_me'] ?? $profile->bio);
        $profile->about_me = $validated['about_me'] ?? ($validated['bio'] ?? $profile->about_me);
        $profile->save();

        // Update skills if provided
        if (isset($validated['skills'])) {
            $profile->skills()->delete();
            $skillNames = array_filter(array_map('trim', explode(',', $validated['skills'])));
            foreach ($skillNames as $skName) {
                Skill::create([
                    'profile_id' => $profile->id,
                    'name' => $skName,
                    'proficiency' => 85,
                    'proficiency_level' => 'Advanced',
                ]);
            }
        }

        // Update interests if provided
        if (isset($validated['interests'])) {
            $profile->interests()->delete();
            $interestNames = array_filter(array_map('trim', explode(',', $validated['interests'])));
            foreach ($interestNames as $intName) {
                Interest::create([
                    'profile_id' => $profile->id,
                    'name' => $intName,
                    'category' => 'General',
                ]);
            }
        }

        // Update student projects if provided
        if (isset($validated['projects'])) {
            $profile->studentProjects()->delete();
            $projTitles = array_filter(array_map('trim', explode(',', $validated['projects'])));
            foreach ($projTitles as $pTitle) {
                \App\Models\StudentProject::create([
                    'profile_id' => $profile->id,
                    'title' => $pTitle,
                    'description' => 'Academic project',
                ]);
            }
        }

        // Refreshed JWT Token
        $jwtToken = JwtService::generateToken($user);
        session(['jwt_token' => $jwtToken]);
        cookie()->queue('jwt_token', $jwtToken, 60 * 24 * 7);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'token' => $jwtToken,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'department' => $profile->department,
                    'location' => $profile->location_name,
                    'skills' => $validated['skills'] ?? '',
                    'interests' => $validated['interests'] ?? '',
                    'bio' => $profile->bio,
                ],
            ], 200);
        }

        return back()
            ->with('success', 'All profile details, skills, and projects saved to database successfully!')
            ->withCookie(cookie('jwt_token', $jwtToken, 60 * 24 * 7));
    }

    /**
     * Get authenticated user details via JWT token API.
     */
    public function me(Request $request)
    {
        $user = JwtService::getUserFromRequest($request);
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated JWT token.'], 401);
        }

        $user->load(['profile.skills', 'profile.interests', 'profile.studentProjects']);
        $p = $user->profile;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'department' => $p->department ?? 'Computer Science',
            'skills' => ($p && $p->skills) ? $p->skills->pluck('name')->implode(', ') : '',
            'interests' => ($p && $p->interests) ? $p->interests->pluck('name')->implode(', ') : '',
            'bio' => $p->bio ?? ($p->about_me ?? ''),
        ], 200);
    }

    /**
     * Log out user and clear JWT token.
     */
    public function logout(Request $request)
    {
        session()->forget('jwt_token');
        cookie()->queue(cookie()->forget('jwt_token'));
        Auth::logout();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
