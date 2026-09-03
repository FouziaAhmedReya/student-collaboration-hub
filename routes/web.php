<?php

use App\Http\Controllers\Common\AdminController;
use App\Http\Controllers\Modules\Fouzia\BookMarketplaceController;
use App\Http\Controllers\Modules\Fouzia\NoteController;
use App\Http\Controllers\Modules\Fouzia\ResourceRequestController;
use App\Http\Controllers\Modules\Fouzia\TutorFinderController;
use App\Http\Controllers\Modules\Rayhan\ProfileSkillController;
use App\Http\Controllers\Modules\Rayhan\StudyGroupController;
use App\Http\Controllers\Modules\Rayhan\StudyGroupMemberController;
use App\Http\Controllers\Modules\Sayeefa\FileSharingController;
use App\Http\Controllers\Modules\Sayeefa\GroupChatController;
use App\Http\Controllers\Modules\Sayeefa\MeetingSchedulerController;
use App\Http\Controllers\Modules\Sayeefa\NotificationController;
use App\Http\Controllers\Modules\Sayeefa\ProjectTaskController;
use App\Http\Controllers\Modules\Tuli\EventAnnouncementController;
use App\Http\Controllers\Modules\Tuli\JwtAuthController;
use App\Http\Controllers\Modules\Tuli\ProgressDashboardController;
use App\Http\Controllers\Modules\Tuli\ProjectIdeaGeneratorController;
use App\Http\Controllers\Modules\Tuli\TeamRecommendationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Homepage
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');

/*
|--------------------------------------------------------------------------
| Fouzia - Notes Sharing and Used Book Marketplace
|--------------------------------------------------------------------------
|
| Only registered Students can access these two features.
|
*/

Route::middleware([
    'auth',
    'role:student',
])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Notes Sharing System
    |--------------------------------------------------------------------------
    */

    Route::controller(NoteController::class)
        ->prefix('notes')
        ->name('notes.')
        ->group(function () {
            Route::get(
                '/',
                'index'
            )->name('index');

            Route::get(
                '/create',
                'create'
            )->name('create');

            Route::post(
                '/',
                'store'
            )->name('store');

            Route::get(
                '/{note}/edit',
                'edit'
            )->name('edit');

            Route::put(
                '/{note}',
                'update'
            )->name('update');

            Route::delete(
                '/{note}',
                'destroy'
            )->name('destroy');

            Route::get(
                '/{note}/preview',
                'preview'
            )->name('preview');

            Route::get(
                '/{note}/download',
                'download'
            )->name('download');
        });

    /*
    |--------------------------------------------------------------------------
    | Used Book Marketplace
    |--------------------------------------------------------------------------
    */

    Route::controller(
        BookMarketplaceController::class
    )
        ->prefix('marketplace')
        ->name('marketplace.')
        ->group(function () {
            Route::get(
                '/',
                'index'
            )->name('index');

            Route::get(
                '/sell',
                'create'
            )->name('create');

            Route::post(
                '/',
                'store'
            )->name('store');

            Route::get(
                '/activity',
                'manage'
            )->name('manage');

            Route::post(
                '/books/{book}/purchase',
                'purchase'
            )->name('orders.store');

            Route::patch(
                '/orders/{order}/accept',
                'acceptOrder'
            )->name('orders.accept');

            Route::patch(
                '/orders/{order}/reject',
                'rejectOrder'
            )->name('orders.reject');

            Route::patch(
                '/orders/{order}/cancel',
                'cancelOrder'
            )->name('orders.cancel');

            Route::patch(
                '/books/{book}/relist',
                'relist'
            )->name('relist');

            Route::get(
                '/{book}',
                'show'
            )->name('show');

            Route::get(
                '/{book}/edit',
                'edit'
            )->name('edit');

            Route::put(
                '/{book}',
                'update'
            )->name('update');

            Route::delete(
                '/{book}',
                'destroy'
            )->name('destroy');
        });
});

/*
|--------------------------------------------------------------------------
| Fouzia - Tutor Finder
|--------------------------------------------------------------------------
|
| Students and approved Tutors can view and search Tutor Finder.
|
*/

Route::middleware([
    'auth',
    'role:student,tutor',
])
    ->prefix('tutors')
    ->name('tutors.')
    ->group(function () {
        Route::get(
            '/',
            [
                TutorFinderController::class,
                'index',
            ]
        )->name('index');
    });

/*
|--------------------------------------------------------------------------
| Fouzia - Tutor Profile and Teaching Materials
|--------------------------------------------------------------------------
|
| Only an approved Tutor can create and manage their Tutor profile.
|
*/

Route::middleware([
    'auth',
    'role:tutor',
])
    ->prefix('tutors')
    ->name('tutors.')
    ->group(function () {
        Route::post(
            '/',
            [
                TutorFinderController::class,
                'store',
            ]
        )->name('store');

        Route::post(
            '/{tutor}/materials',
            [
                TutorFinderController::class,
                'uploadMaterial',
            ]
        )->name('materials.store');

        Route::delete(
            '/{tutor}/materials/{material}',
            [
                TutorFinderController::class,
                'destroyMaterial',
            ]
        )->name('materials.destroy');

        Route::delete(
            '/{tutor}',
            [
                TutorFinderController::class,
                'destroy',
            ]
        )->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Fouzia - Tutor Ratings
|--------------------------------------------------------------------------
|
| Only Students can submit or update a Tutor rating.
|
*/

Route::middleware([
    'auth',
    'role:student',
])->post(
    '/tutors/{tutor}/ratings',
    [
        TutorFinderController::class,
        'rate',
    ]
)->name('tutors.ratings.store');

/*
|--------------------------------------------------------------------------
| Fouzia - Resource Request System
|--------------------------------------------------------------------------
|
| Students and approved Tutors can view resource requests.
|
*/

Route::middleware([
    'auth',
    'role:student,tutor',
])->get(
    '/resource-requests',
    [
        ResourceRequestController::class,
        'index',
    ]
)->name('resource-requests.index');

/*
|--------------------------------------------------------------------------
| Fouzia - Create Resource Request
|--------------------------------------------------------------------------
|
| Only Students can create resource requests.
|
*/

Route::middleware([
    'auth',
    'role:student',
])->post(
    '/resource-requests',
    [
        ResourceRequestController::class,
        'store',
    ]
)->name('resource-requests.store');

/*
|--------------------------------------------------------------------------
| Fouzia - Upload Requested Resource
|--------------------------------------------------------------------------
|
| Students and approved Tutors can upload requested resources.
|
*/

Route::middleware([
    'auth',
    'role:student,tutor',
])->post(
    '/resource-requests/{resourceRequest}/uploads',
    [
        ResourceRequestController::class,
        'upload',
    ]
)->name('resource-requests.uploads.store');

/*
|--------------------------------------------------------------------------
| Tuli Module - Project Idea Generator
|--------------------------------------------------------------------------
*/

Route::prefix('project-ideas')
    ->name('project-ideas.')
    ->group(function () {
        Route::get(
            '/',
            [
                ProjectIdeaGeneratorController::class,
                'index',
            ]
        )->name('index');

        Route::post(
            '/generate',
            [
                ProjectIdeaGeneratorController::class,
                'generate',
            ]
        )->name('generate');

        Route::put(
            '/{idea}',
            [
                ProjectIdeaGeneratorController::class,
                'update',
            ]
        )->name('update');

        Route::delete(
            '/{idea}',
            [
                ProjectIdeaGeneratorController::class,
                'destroy',
            ]
        )->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Tuli Module - Team Recommendations
|--------------------------------------------------------------------------
*/

Route::prefix('team-recommendations')
    ->name('team-recommendations.')
    ->group(function () {
        Route::get(
            '/',
            [
                TeamRecommendationController::class,
                'index',
            ]
        )->name('index');

        Route::post(
            '/match',
            [
                TeamRecommendationController::class,
                'match',
            ]
        )->name('match');
    });

/*
|--------------------------------------------------------------------------
| Tuli Module - Progress Dashboard
|--------------------------------------------------------------------------
*/

Route::prefix('progress-dashboard')
    ->name('progress-dashboard.')
    ->group(function () {
        Route::get(
            '/',
            [
                ProgressDashboardController::class,
                'index',
            ]
        )->name('index');
    });

/*
|--------------------------------------------------------------------------
| Tuli Module - Events
|--------------------------------------------------------------------------
*/

Route::prefix('events')
    ->name('events.')
    ->group(function () {
        Route::get(
            '/',
            [
                EventAnnouncementController::class,
                'index',
            ]
        )->name('index');

        Route::post(
            '/',
            [
                EventAnnouncementController::class,
                'store',
            ]
        )->name('store');

        Route::put(
            '/{event}',
            [
                EventAnnouncementController::class,
                'update',
            ]
        )->name('update');

        Route::delete(
            '/{event}',
            [
                EventAnnouncementController::class,
                'destroy',
            ]
        )->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Tuli Module - API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    Route::get(
        '/ideas',
        [
            ProjectIdeaGeneratorController::class,
            'index',
        ]
    );

    Route::post(
        '/ideas/generate',
        [
            ProjectIdeaGeneratorController::class,
            'generate',
        ]
    );

    Route::put(
        '/ideas/{idea}',
        [
            ProjectIdeaGeneratorController::class,
            'update',
        ]
    );

    Route::delete(
        '/ideas/{idea}',
        [
            ProjectIdeaGeneratorController::class,
            'destroy',
        ]
    );

    Route::get(
        '/teammates',
        [
            TeamRecommendationController::class,
            'index',
        ]
    );

    Route::post(
        '/teams/match',
        [
            TeamRecommendationController::class,
            'match',
        ]
    );

    Route::get(
        '/progress',
        [
            ProgressDashboardController::class,
            'apiSummary',
        ]
    );

    Route::get(
        '/events',
        [
            EventAnnouncementController::class,
            'index',
        ]
    );

    Route::post(
        '/events',
        [
            EventAnnouncementController::class,
            'store',
        ]
    );

    Route::put(
        '/events/{event}',
        [
            EventAnnouncementController::class,
            'update',
        ]
    );

    Route::delete(
        '/events/{event}',
        [
            EventAnnouncementController::class,
            'destroy',
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication API
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/auth/login',
        [
            JwtAuthController::class,
            'login',
        ]
    );

    Route::post(
        '/auth/register',
        [
            JwtAuthController::class,
            'register',
        ]
    );

    Route::get(
        '/auth/user',
        [
            JwtAuthController::class,
            'me',
        ]
    );

    Route::put(
        '/auth/profile',
        [
            JwtAuthController::class,
            'updateProfile',
        ]
    );

    Route::post(
        '/auth/logout',
        [
            JwtAuthController::class,
            'logout',
        ]
    );
});

/*
|--------------------------------------------------------------------------
| Sayeefa Module
|--------------------------------------------------------------------------
|
| Project Task Management, Group Chat, Meeting Scheduler, File Sharing
| and Notifications require login.
|
*/

Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Project Task Management
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/tasks',
        [
            ProjectTaskController::class,
            'index',
        ]
    )->name('tasks.index');

    Route::prefix('api')->group(function () {
        Route::get(
            '/projects',
            [
                ProjectTaskController::class,
                'apiProjects',
            ]
        );

        Route::post(
            '/projects',
            [
                ProjectTaskController::class,
                'storeProject',
            ]
        );

        Route::post(
            '/projects/{project}/join',
            [
                ProjectTaskController::class,
                'joinProject',
            ]
        );

        Route::get(
            '/tasks',
            [
                ProjectTaskController::class,
                'apiTasks',
            ]
        );

        Route::post(
            '/tasks',
            [
                ProjectTaskController::class,
                'store',
            ]
        );

        Route::put(
            '/tasks/{task}',
            [
                ProjectTaskController::class,
                'update',
            ]
        );

        Route::delete(
            '/tasks/{task}',
            [
                ProjectTaskController::class,
                'destroy',
            ]
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Group Chat System
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/group-chat',
        [
            GroupChatController::class,
            'index',
        ]
    )->name('group-chat.index');

    Route::prefix('api')->group(function () {
        Route::get(
            '/chat-groups',
            [
                GroupChatController::class,
                'apiGroups',
            ]
        );

        Route::post(
            '/chat-groups',
            [
                GroupChatController::class,
                'storeGroup',
            ]
        );

        Route::post(
            '/chat-groups/{group}/join',
            [
                GroupChatController::class,
                'joinGroup',
            ]
        );

        Route::get(
            '/chat-groups/{group}/messages',
            [
                GroupChatController::class,
                'apiMessages',
            ]
        );

        Route::post(
            '/chat-groups/{group}/messages',
            [
                GroupChatController::class,
                'sendMessage',
            ]
        );

        Route::get(
            '/chat-groups/{group}/meetings',
            [
                GroupChatController::class,
                'apiMeetings',
            ]
        );

        Route::post(
            '/chat-groups/{group}/meetings',
            [
                GroupChatController::class,
                'storeMeeting',
            ]
        );

        Route::delete(
            '/meetings/{meeting}',
            [
                GroupChatController::class,
                'destroyMeeting',
            ]
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Meeting Scheduler
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/meetings',
        [
            MeetingSchedulerController::class,
            'index',
        ]
    )->name('meetings.index');

    Route::prefix('api')->group(function () {
        Route::get(
            '/meetings',
            [
                MeetingSchedulerController::class,
                'apiMeetings',
            ]
        );

        Route::post(
            '/meetings',
            [
                MeetingSchedulerController::class,
                'store',
            ]
        );

        Route::put(
            '/meetings/{meeting}',
            [
                MeetingSchedulerController::class,
                'update',
            ]
        );

        Route::delete(
            '/meetings/{meeting}',
            [
                MeetingSchedulerController::class,
                'destroy',
            ]
        );
    });

    /*
    |--------------------------------------------------------------------------
    | File Sharing
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/files',
        [
            FileSharingController::class,
            'index',
        ]
    )->name('files.index');

    Route::prefix('api')->group(function () {
        Route::get(
            '/files',
            [
                FileSharingController::class,
                'apiFiles',
            ]
        );

        Route::post(
            '/files',
            [
                FileSharingController::class,
                'store',
            ]
        );

        Route::get(
            '/projects/{project}/meetings-and-tasks',
            [
                FileSharingController::class,
                'apiProjectContext',
            ]
        );

        Route::delete(
            '/files/{file}',
            [
                FileSharingController::class,
                'destroy',
            ]
        );
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/notifications',
        [
            NotificationController::class,
            'index',
        ]
    )->name('notifications.index');

    Route::prefix('api')->group(function () {
        Route::get(
            '/notifications',
            [
                NotificationController::class,
                'apiIndex',
            ]
        );

        Route::post(
            '/notifications/{id}/read',
            [
                NotificationController::class,
                'markRead',
            ]
        );

        Route::post(
            '/notifications/read-all',
            [
                NotificationController::class,
                'markAllRead',
            ]
        );
    });
});

/*
|--------------------------------------------------------------------------
| Rayhan Module - Student Profile and Skill Management
|--------------------------------------------------------------------------
*/

Route::prefix('profile')
    ->name('profile.')
    ->group(function () {
        Route::get(
            '/',
            [
                ProfileSkillController::class,
                'index',
            ]
        )->name('index');

        Route::get(
            '/edit',
            [
                ProfileSkillController::class,
                'edit',
            ]
        )->name('edit');

        Route::put(
            '/',
            [
                ProfileSkillController::class,
                'update',
            ]
        )->name('update');

        /*
        |--------------------------------------------------------------------------
        | Skills
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/skills',
            [
                ProfileSkillController::class,
                'storeSkill',
            ]
        )->name('skills.store');

        Route::put(
            '/skills/{skill}',
            [
                ProfileSkillController::class,
                'updateSkill',
            ]
        )->name('skills.update');

        Route::delete(
            '/skills/{skill}',
            [
                ProfileSkillController::class,
                'destroySkill',
            ]
        )->name('skills.destroy');

        /*
        |--------------------------------------------------------------------------
        | Interests
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/interests/suggestions',
            [
                ProfileSkillController::class,
                'interestSuggestions',
            ]
        )->name('interests.suggestions');

        Route::post(
            '/interests',
            [
                ProfileSkillController::class,
                'storeInterest',
            ]
        )->name('interests.store');

        Route::put(
            '/interests/{interest}',
            [
                ProfileSkillController::class,
                'updateInterest',
            ]
        )->name('interests.update');

        Route::delete(
            '/interests/{interest}',
            [
                ProfileSkillController::class,
                'destroyInterest',
            ]
        )->name('interests.destroy');

        /*
        |--------------------------------------------------------------------------
        | Completed Student Projects
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/projects',
            [
                ProfileSkillController::class,
                'storeProject',
            ]
        )->name('projects.store');

        Route::put(
            '/projects/{project}',
            [
                ProfileSkillController::class,
                'updateProject',
            ]
        )->name('projects.update');

        Route::delete(
            '/projects/{project}',
            [
                ProfileSkillController::class,
                'destroyProject',
            ]
        )->name('projects.destroy');

        /*
        |--------------------------------------------------------------------------
        | Portfolio Links
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/portfolio-links',
            [
                ProfileSkillController::class,
                'storePortfolioLink',
            ]
        )->name('portfolio-links.store');

        Route::put(
            '/portfolio-links/{link}',
            [
                ProfileSkillController::class,
                'updatePortfolioLink',
            ]
        )->name('portfolio-links.update');

        Route::delete(
            '/portfolio-links/{link}',
            [
                ProfileSkillController::class,
                'destroyPortfolioLink',
            ]
        )->name('portfolio-links.destroy');
    });

/*
|--------------------------------------------------------------------------
| Direct Interest Suggestions Alias
|--------------------------------------------------------------------------
*/

Route::get(
    '/interests/suggestions',
    [
        ProfileSkillController::class,
        'interestSuggestions',
    ]
)->name('interests.suggestions.direct');

/*
|--------------------------------------------------------------------------
| Rayhan Module - Study Group Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::controller(StudyGroupController::class)
        ->prefix('groups')
        ->name('groups.')
        ->group(function () {
            Route::get(
                '/',
                'index'
            )->name('index');

            Route::get(
                '/create',
                'create'
            )->name('create');

            Route::post(
                '/',
                'store'
            )->name('store');

            Route::get(
                '/{group}/edit',
                'edit'
            )->name('edit');

            Route::put(
                '/{group}',
                'update'
            )->name('update');

            Route::delete(
                '/{group}',
                'destroy'
            )->name('destroy');

            Route::post(
                '/{group}/join',
                'join'
            )->name('join');

            Route::post(
                '/{group}/leave',
                'leave'
            )->name('leave');
        });

    /*
    |--------------------------------------------------------------------------
    | Study Group Member Management
    |--------------------------------------------------------------------------
    */

    Route::controller(
        StudyGroupMemberController::class
    )
        ->prefix('groups/{group}/members')
        ->name('groups.members.')
        ->group(function () {
            Route::get(
                '/',
                'index'
            )->name('index');

            Route::post(
                '/invite',
                'invite'
            )->name('invite');

            Route::patch(
                '/{member}/status',
                'updateStatus'
            )->name('updateStatus');

            Route::patch(
                '/{member}/role',
                'updateRole'
            )->name('updateRole');

            Route::delete(
                '/{member}',
                'destroy'
            )->name('destroy');
        });
});

/*
|--------------------------------------------------------------------------
| Role-Based Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin' =>
                redirect()->route(
                    'admin.dashboard'
                ),

            'tutor' =>
                redirect()->route(
                    'tutors.index'
                ),

            default =>
                redirect()->route(
                    'notes.index'
                ),
        };
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Login, Registration and Logout Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'admin',
])->group(function () {
    Route::get(
        '/admin/reports',
        [
            AdminController::class,
            'reports',
        ]
    )->name('admin.reports');

    Route::post(
        '/admin/report/{id}/resolve',
        [
            AdminController::class,
            'resolveReport',
        ]
    )->name('admin.report.resolve');

    Route::post(
        '/admin/report/{id}/reject',
        [
            AdminController::class,
            'rejectReport',
        ]
    )->name('admin.report.reject');

    Route::post(
        '/admin/tutor/{id}/approve',
        [
            AdminController::class,
            'approveTutor',
        ]
    )->name('admin.tutor.approve');

    Route::post(
        '/admin/tutor/{id}/reject',
        [
            AdminController::class,
            'rejectTutor',
        ]
    )->name('admin.tutor.reject');

    Route::get(
        '/admin/dashboard',
        [
            AdminController::class,
            'dashboard',
        ]
    )->name('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| User Reporting Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get(
        '/report/{user}',
        [
            ReportController::class,
            'create',
        ]
    )->name('report.create');

    Route::post(
        '/report',
        [
            ReportController::class,
            'store',
        ]
    )->name('report.store');
});
/*
|--------------------------------------------------------------------------
| Admin Content Moderation Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get(
        '/admin/content',
        [
            \App\Http\Controllers\Common\AdminController::class,
            'content',
        ]
    )->name('admin.content');

    Route::delete(
        '/admin/content/notes/{note}',
        [
            \App\Http\Controllers\Common\AdminController::class,
            'destroyNote',
        ]
    )->name('admin.content.notes.destroy');

    Route::delete(
        '/admin/content/books/{book}',
        [
            \App\Http\Controllers\Common\AdminController::class,
            'destroyBook',
        ]
    )->name('admin.content.books.destroy');

    Route::delete(
        '/admin/content/tutor-materials/{material}',
        [
            \App\Http\Controllers\Common\AdminController::class,
            'destroyTutorMaterial',
        ]
    )->name('admin.content.tutor-materials.destroy');

    Route::delete(
        '/admin/content/resource-uploads/{upload}',
        [
            \App\Http\Controllers\Common\AdminController::class,
            'destroyResourceUpload',
        ]
    )->name('admin.content.resource-uploads.destroy');
});