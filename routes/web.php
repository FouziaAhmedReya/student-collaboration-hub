<?php

use App\Http\Controllers\Modules\Fouzia\NoteController;
use App\Http\Controllers\Modules\Tuli\ProjectIdeaGeneratorController;
use App\Http\Controllers\Modules\Tuli\TeamRecommendationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Sayeefa\ProjectTaskController;
use App\Http\Controllers\Modules\Sayeefa\GroupChatController;
use App\Http\Controllers\Modules\Fouzia\BookMarketplaceController;
use App\Http\Controllers\Modules\Rayhan\ProfileSkillController;
use App\Http\Controllers\Modules\Rayhan\StudyGroupController;
use App\Http\Controllers\Modules\Rayhan\StudyGroupMemberController;
use App\Http\Controllers\Modules\Sayeefa\MeetingSchedulerController;
use App\Http\Controllers\Modules\Sayeefa\FileSharingController;
Route::redirect('/', '/notes');

// Login fallback route for guest redirection / Breeze compatibility
Route::get('/login', function () {
    return redirect()->route('profile.index');
})->name('login');

// Fouzia Module - Notes
Route::controller(NoteController::class)->prefix('notes')->name('notes.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{note}/edit', 'edit')->name('edit');
    Route::put('/{note}', 'update')->name('update');
    Route::delete('/{note}', 'destroy')->name('destroy');
    Route::get('/{note}/preview', 'preview')->name('preview');
    Route::get('/{note}/download', 'download')->name('download');
});

// Fouzia Module - Used Book Marketplace
Route::controller(BookMarketplaceController::class)
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

// Tuli Module - Web Routes
Route::prefix('project-ideas')->name('project-ideas.')->group(function () {
    Route::get('/', [ProjectIdeaGeneratorController::class, 'index'])->name('index');
    Route::post('/generate', [ProjectIdeaGeneratorController::class, 'generate'])->name('generate');
    Route::put('/{idea}', [ProjectIdeaGeneratorController::class, 'update'])->name('update');
    Route::delete('/{idea}', [ProjectIdeaGeneratorController::class, 'destroy'])->name('destroy');
});

Route::prefix('team-recommendations')->name('team-recommendations.')->group(function () {
    Route::get('/', [TeamRecommendationController::class, 'index'])->name('index');
    Route::post('/match', [TeamRecommendationController::class, 'match'])->name('match');
});

// Tuli Module - API Endpoints (matching tuli_saha backend specs)
Route::prefix('api')->group(function () {
    Route::get('/ideas', [ProjectIdeaGeneratorController::class, 'index']);
    Route::post('/ideas/generate', [ProjectIdeaGeneratorController::class, 'generate']);
    Route::put('/ideas/{idea}', [ProjectIdeaGeneratorController::class, 'update']);
    Route::delete('/ideas/{idea}', [ProjectIdeaGeneratorController::class, 'destroy']);
    Route::get('/teammates', [TeamRecommendationController::class, 'index']);
    Route::post('/teams/match', [TeamRecommendationController::class, 'match']);
});
// Sayeefa Module - Project Task Management (Web)
Route::get('/tasks', [ProjectTaskController::class, 'index'])->name('tasks.index');

// Sayeefa Module - Project Task Management (API)
Route::prefix('api')->group(function () {
    Route::get('/projects', [ProjectTaskController::class, 'apiProjects']);
    Route::post('/projects', [ProjectTaskController::class, 'storeProject']);
    Route::get('/tasks', [ProjectTaskController::class, 'apiTasks']);
    Route::post('/tasks', [ProjectTaskController::class, 'store']);
    Route::put('/tasks/{task}', [ProjectTaskController::class, 'update']);
    Route::delete('/tasks/{task}', [ProjectTaskController::class, 'destroy']);
});

// Sayeefa Module - Group Chat (Web)
Route::get('/group-chat', [GroupChatController::class, 'index'])->name('group-chat.index');

// Sayeefa Module - Group Chat (API)
Route::prefix('api')->group(function () {
    Route::get('/chat-groups', [GroupChatController::class, 'apiGroups']);
    Route::post('/chat-groups', [GroupChatController::class, 'storeGroup']);
    Route::get('/chat-groups/{group}/messages', [GroupChatController::class, 'apiMessages']);
    Route::post('/chat-groups/{group}/messages', [GroupChatController::class, 'sendMessage']);
    Route::get('/chat-groups/{group}/meetings', [GroupChatController::class, 'apiMeetings']);
    Route::post('/chat-groups/{group}/meetings', [GroupChatController::class, 'storeMeeting']);
    Route::delete('/meetings/{meeting}', [GroupChatController::class, 'destroyMeeting']);
});
// Sayeefa Module - Meeting Scheduler (Web)
Route::get('/meetings', [MeetingSchedulerController::class, 'index'])->name('meetings.index');

// Sayeefa Module - Meeting Scheduler (API)
Route::prefix('api')->group(function () {
    Route::get('/meetings', [MeetingSchedulerController::class, 'apiMeetings']);
    Route::post('/meetings', [MeetingSchedulerController::class, 'store']);
    Route::put('/meetings/{meeting}', [MeetingSchedulerController::class, 'update']);
    Route::delete('/meetings/{meeting}', [MeetingSchedulerController::class, 'destroy']);
});

// Sayeefa Module - File Sharing (Web)
Route::get('/files', [FileSharingController::class, 'index'])->name('files.index');

// Sayeefa Module - File Sharing (API)
Route::prefix('api')->group(function () {
    Route::get('/files', [FileSharingController::class, 'apiFiles']);
    Route::post('/files', [FileSharingController::class, 'store']);
    Route::get('/projects/{project}/meetings-and-tasks', [FileSharingController::class, 'apiProjectContext']);
    Route::delete('/files/{file}', [FileSharingController::class, 'destroy']);
});
// Rayhan Module 1 - Student Profile & Skill Management
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileSkillController::class, 'index'])->name('index');
    Route::get('/edit', [ProfileSkillController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileSkillController::class, 'update'])->name('update');

    // Skills
    Route::post('/skills', [ProfileSkillController::class, 'storeSkill'])->name('skills.store');
    Route::put('/skills/{skill}', [ProfileSkillController::class, 'updateSkill'])->name('skills.update');
    Route::delete('/skills/{skill}', [ProfileSkillController::class, 'destroySkill'])->name('skills.destroy');

    // Interests
    Route::get('/interests/suggestions', [ProfileSkillController::class, 'interestSuggestions'])->name('interests.suggestions');
    Route::post('/interests', [ProfileSkillController::class, 'storeInterest'])->name('interests.store');
    Route::put('/interests/{interest}', [ProfileSkillController::class, 'updateInterest'])->name('interests.update');
    Route::delete('/interests/{interest}', [ProfileSkillController::class, 'destroyInterest'])->name('interests.destroy');

    // Student Completed Projects (student_projects table)
    Route::post('/projects', [ProfileSkillController::class, 'storeProject'])->name('projects.store');
    Route::put('/projects/{project}', [ProfileSkillController::class, 'updateProject'])->name('projects.update');
    Route::delete('/projects/{project}', [ProfileSkillController::class, 'destroyProject'])->name('projects.destroy');

    // Portfolio Links
    Route::post('/portfolio-links', [ProfileSkillController::class, 'storePortfolioLink'])->name('portfolio-links.store');
    Route::put('/portfolio-links/{link}', [ProfileSkillController::class, 'updatePortfolioLink'])->name('portfolio-links.update');
    Route::delete('/portfolio-links/{link}', [ProfileSkillController::class, 'destroyPortfolioLink'])->name('portfolio-links.destroy');
});

// Direct alias for interest suggestions
Route::get('/interests/suggestions', [ProfileSkillController::class, 'interestSuggestions'])->name('interests.suggestions.direct');

// Module 2 - Study Group Management
Route::middleware(['auth'])->group(function () {
    Route::controller(StudyGroupController::class)->prefix('groups')->name('groups.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{group}/edit', 'edit')->name('edit');
        Route::put('/{group}', 'update')->name('update');
        Route::delete('/{group}', 'destroy')->name('destroy');
        Route::post('/{group}/join', 'join')->name('join');
        Route::post('/{group}/leave', 'leave')->name('leave');
    });

    // Module 2 - Study Group Members Management
    Route::controller(StudyGroupMemberController::class)->prefix('groups/{group}/members')->name('groups.members.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/invite', 'invite')->name('invite');
        Route::patch('/{member}/status', 'updateStatus')->name('updateStatus');
        Route::patch('/{member}/role', 'updateRole')->name('updateRole');
        Route::delete('/{member}', 'destroy')->name('destroy');
    });
});
