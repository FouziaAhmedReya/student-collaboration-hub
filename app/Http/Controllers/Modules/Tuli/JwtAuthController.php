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
     * Show the login form.
     *
     * Do not redirect using the JWT cookie here.
     * Doing so can cause a /login and /notes redirect loop.
     */
    public function showLoginForm()
    {
        return view('modules.tuli.auth.login');
    }

    /**
     * Log in a registered user.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ]);

        $user = User::where(
            'email',
            strtolower($validated['email'])
        )->first();

        if (
            ! $user
            || ! Hash::check(
                $validated['password'],
                $user->password
            )
        ) {
            if (
                $request->wantsJson()
                || $request->is('api/*')
            ) {
                return response()->json([
                    'error' =>
                        'Invalid email or password credentials.',
                ], 401);
            }

            return back()
                ->withErrors([
                    'email' =>
                        'Invalid email or password credentials.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Tutor approval check
        |--------------------------------------------------------------------------
        |
        | Tutor accounts remain pending until an administrator approves them.
        | Student accounts do not require administrator approval.
        |
        */

        if (
            $user->role === 'tutor'
            && $user->status !== 'approved'
        ) {
            $message = $user->status === 'rejected'
                ? 'Your tutor registration was rejected. Please contact the administrator.'
                : 'Your tutor account is waiting for administrator approval.';

            if (
                $request->wantsJson()
                || $request->is('api/*')
            ) {
                return response()->json([
                    'error' => $message,
                ], 403);
            }

            return back()
                ->withErrors([
                    'email' => $message,
                ])
                ->withInput();
        }

        /*
         * Start Laravel session authentication.
         */
        Auth::login($user);

        /*
         * Regenerate the session after login.
         * This makes the Laravel auth middleware recognize the user.
         */
        $request->session()->regenerate();

        /*
         * Generate JWT for the existing API/JWT features.
         */
        $jwtToken = JwtService::generateToken($user);

        session([
            'jwt_token' => $jwtToken,
        ]);

        cookie()->queue(
            'jwt_token',
            $jwtToken,
            60 * 24 * 7
        );

        if (
            $request->wantsJson()
            || $request->is('api/*')
        ) {
            return response()->json([
                'message' => 'Login successful',

                'token' => $jwtToken,

                'token_type' => 'Bearer',

                'user' => [
                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'role' => $user->role,

                    'status' => $user->status,
                ],
            ], 200);
        }

        return redirect()
            ->route(
                $this->homeRouteFor($user)
            )
            ->with(
                'success',
                "Welcome back, {$user->name}!"
            );
    }

    /**
     * Show the registration form.
     *
     * Do not redirect using the JWT cookie here.
     * Laravel's guest middleware handles authenticated users.
     */
    public function showRegisterForm()
    {
        return view('modules.tuli.auth.register');
    }

    /**
     * Register a Student or Tutor.
     *
     * Students are approved and logged in immediately.
     * Tutors are stored as pending and wait for administrator approval.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            /*
             * Any valid unused email address can register.
             * There is no university-domain restriction.
             * There is no email-verification requirement.
             */
            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            /*
             * The user selects Student or Tutor.
             * Users cannot register themselves as administrators.
             */
            'role' => [
                'required',
                'in:student,tutor',
            ],

            'student_id' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'department' => [
                'nullable',
                'string',
                'max:255',
            ],

            'skills' => [
                'nullable',
                'string',
            ],

            'interests' => [
                'nullable',
                'string',
            ],

            'bio' => [
                'nullable',
                'string',
            ],

            'about_me' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create user
        |--------------------------------------------------------------------------
        |
        | Student: role=student and status=approved
        | Tutor:   role=tutor and status=pending
        |
        */

        $user = User::create([
            'name' => $validated['name'],

            'email' => strtolower(
                $validated['email']
            ),

            'password' => Hash::make(
                $validated['password']
            ),

            'role' => $validated['role'],

            'status' =>
                $validated['role'] === 'tutor'
                    ? 'pending'
                    : 'approved',

            'student_id' =>
                $validated['student_id'] ?? null,

            'department' =>
                $validated['department'] ?? null,

            'phone' =>
                $validated['phone'] ?? null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create profile
        |--------------------------------------------------------------------------
        */

        $profile = Profile::create([
            'user_id' => $user->id,

            'department' =>
                $validated['department']
                ?? 'Computer Science',

            'phone' =>
                $validated['phone'] ?? null,

            'about_me' =>
                $validated['about_me']
                ?? ($validated['bio'] ?? null),

            'bio' =>
                $validated['bio']
                ?? ($validated['about_me'] ?? null),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Save skills
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['skills'])) {
            $skillNames = array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        $validated['skills']
                    )
                )
            );

            foreach ($skillNames as $skillName) {
                Skill::create([
                    'profile_id' => $profile->id,

                    'name' => $skillName,

                    'proficiency' => 80,

                    'proficiency_level' =>
                        'Advanced',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Save interests
        |--------------------------------------------------------------------------
        */

        if (! empty($validated['interests'])) {
            $interestNames = array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        $validated['interests']
                    )
                )
            );

            foreach ($interestNames as $interestName) {
                Interest::create([
                    'profile_id' => $profile->id,

                    'name' => $interestName,

                    'category' => 'General',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Tutor registration
        |--------------------------------------------------------------------------
        |
        | The tutor account is created with pending status.
        | The tutor is not logged in until an administrator approves it.
        |
        */

        if ($user->role === 'tutor') {
            if (
                $request->wantsJson()
                || $request->is('api/*')
            ) {
                return response()->json([
                    'message' =>
                        'Tutor registration submitted and waiting for administrator approval.',

                    'user' => [
                        'id' => $user->id,

                        'name' => $user->name,

                        'email' => $user->email,

                        'role' => $user->role,

                        'status' => $user->status,
                    ],
                ], 202);
            }

            return redirect()
                ->route('login')
                ->with(
                    'success',
                    'Tutor registration submitted. Wait for administrator approval before logging in.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Student registration
        |--------------------------------------------------------------------------
        |
        | Students are approved and logged in immediately.
        |
        */

        Auth::login($user);

        /*
         * Regenerate the Laravel session so auth middleware
         * recognizes the newly registered student.
         */
        $request->session()->regenerate();

        $jwtToken = JwtService::generateToken($user);

        session([
            'jwt_token' => $jwtToken,
        ]);

        cookie()->queue(
            'jwt_token',
            $jwtToken,
            60 * 24 * 7
        );

        if (
            $request->wantsJson()
            || $request->is('api/*')
        ) {
            return response()->json([
                'message' =>
                    'Student registration successful',

                'token' => $jwtToken,

                'token_type' => 'Bearer',

                'user' => [
                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'role' => $user->role,

                    'status' => $user->status,

                    'student_id' =>
                        $user->student_id,

                    'phone' => $user->phone,

                    'department' =>
                        $profile->department,
                ],
            ], 201);
        }

        return redirect()
            ->route(
                $this->homeRouteFor($user)
            )
            ->with(
                'success',
                "Registration successful! Welcome, {$user->name}."
            );
    }

    /**
     * Show the user's profile form.
     */
    public function showProfileForm(Request $request)
    {
        $user = JwtService::getUserFromRequest(
            $request
        );

        if (! $user) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                        'Please login to access your profile.',
                ]);
        }

        $user->load([
            'profile.skills',

            'profile.interests',

            'profile.studentProjects',

            'profile.portfolioLinks',
        ]);

        $profile = $user->profile;

        $skillsStr =
            $profile && $profile->skills
                ? $profile->skills
                    ->pluck('name')
                    ->implode(', ')
                : '';

        $interestsStr =
            $profile && $profile->interests
                ? $profile->interests
                    ->pluck('name')
                    ->implode(', ')
                : '';

        $projectsStr =
            $profile && $profile->studentProjects
                ? $profile->studentProjects
                    ->pluck('title')
                    ->implode(', ')
                : '';

        $portfolioStr =
            $profile && $profile->portfolioLinks
                ? $profile->portfolioLinks
                    ->pluck('url')
                    ->implode(', ')
                : '';

        $aiEventRecommendations = null;

        return view(
            'modules.tuli.auth.profile',
            compact(
                'user',
                'profile',
                'skillsStr',
                'interestsStr',
                'projectsStr',
                'portfolioStr',
                'aiEventRecommendations'
            )
        );
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = JwtService::getUserFromRequest(
            $request
        );

        if (! $user) {
            if (
                $request->wantsJson()
                || $request->is('api/*')
            ) {
                return response()->json([
                    'error' =>
                        'Unauthenticated JWT token.',
                ], 401);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                        'Please login to access your profile.',
                ]);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'department' => [
                'nullable',
                'string',
                'max:255',
            ],

            'location_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'skills' => [
                'nullable',
                'string',
            ],

            'interests' => [
                'nullable',
                'string',
            ],

            'projects' => [
                'nullable',
                'string',
            ],

            'portfolio' => [
                'nullable',
                'string',
            ],

            'bio' => [
                'nullable',
                'string',
            ],

            'about_me' => [
                'nullable',
                'string',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],
        ]);

        $user->name = $validated['name'];

        if (
            array_key_exists(
                'department',
                $validated
            )
        ) {
            $user->department =
                $validated['department'];
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make(
                $validated['password']
            );
        }

        $user->save();

        $profile = Profile::firstOrCreate([
            'user_id' => $user->id,
        ]);

        if (
            array_key_exists(
                'department',
                $validated
            )
        ) {
            $profile->department =
                $validated['department'];
        }

        if (
            array_key_exists(
                'location_name',
                $validated
            )
        ) {
            $profile->preferred_location_name =
                $validated['location_name'];
        }

        $profile->bio =
            $validated['bio']
            ?? (
                $validated['about_me']
                ?? $profile->bio
            );

        $profile->about_me =
            $validated['about_me']
            ?? (
                $validated['bio']
                ?? $profile->about_me
            );

        $profile->save();

        /*
        |--------------------------------------------------------------------------
        | Update skills
        |--------------------------------------------------------------------------
        */

        if (isset($validated['skills'])) {
            $profile->skills()->delete();

            $skillNames = array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        $validated['skills']
                    )
                )
            );

            foreach ($skillNames as $skillName) {
                Skill::create([
                    'profile_id' => $profile->id,

                    'name' => $skillName,

                    'proficiency' => 85,

                    'proficiency_level' =>
                        'Advanced',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update interests
        |--------------------------------------------------------------------------
        */

        if (isset($validated['interests'])) {
            $profile->interests()->delete();

            $interestNames = array_filter(
                array_map(
                    'trim',
                    explode(
                        ',',
                        $validated['interests']
                    )
                )
            );

            foreach ($interestNames as $interestName) {
                Interest::create([
                    'profile_id' => $profile->id,

                    'name' => $interestName,

                    'category' => 'General',
                ]);
            }
        }

        /*
         * Generate a new JWT after profile or password changes.
         */
        $jwtToken = JwtService::generateToken($user);

        session([
            'jwt_token' => $jwtToken,
        ]);

        cookie()->queue(
            'jwt_token',
            $jwtToken,
            60 * 24 * 7
        );

        return back()->with(
            'success',
            'Profile updated successfully!'
        );
    }

    /**
     * Return the authenticated user's information.
     */
    public function me(Request $request)
    {
        $user = JwtService::getUserFromRequest(
            $request
        );

        if (! $user) {
            return response()->json([
                'error' =>
                    'Unauthenticated JWT token.',
            ], 401);
        }

        $user->load([
            'profile.skills',
            'profile.interests',
        ]);

        $profile = $user->profile;

        return response()->json([
            'id' => $user->id,

            'name' => $user->name,

            'email' => $user->email,

            'role' => $user->role,

            'status' => $user->status,

            'student_id' => $user->student_id,

            'phone' => $user->phone,

            'department' =>
                $profile?->department
                ?? $user->department
                ?? 'Computer Science',
        ], 200);
    }

    /**
     * Log out and remove both Laravel session
     * authentication and the JWT token.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->forget(
            'jwt_token'
        );

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        cookie()->queue(
            cookie()->forget('jwt_token')
        );

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Logged out successfully.'
            );
    }

    /**
     * Choose the correct page after login or registration.
     */
    private function homeRouteFor(User $user): string
    {
        return match ($user->role) {
            'admin' => 'admin.dashboard',

            'tutor' => 'tutors.index',

            default => 'notes.index',
        };
    }
}