<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PortfolioLinkController;
use App\Http\Controllers\ProjectRecruitmentController;
use App\Http\Controllers\StudyGroupController;
use App\Http\Controllers\StudyGroupMemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('profile.show');
});

Route::get('/dashboard', function () {
    return redirect()->route('profile.show');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Module 1: Profile & Skills Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/location', [ProfileController::class, 'updateLocation'])->name('profile.location');

    Route::post('/skills', [SkillController::class, 'store'])->name('skills.store');
    Route::put('/skills/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::delete('/skills/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');

    Route::get('/interests/suggestions', [InterestController::class, 'suggestions'])->name('interests.suggestions');
    Route::post('/interests', [InterestController::class, 'store'])->name('interests.store');
    Route::delete('/interests/{interest}', [InterestController::class, 'destroy'])->name('interests.destroy');

    // Module 1: Student Portfolio Projects (keep exact original routes /projects for Module 1)
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/portfolio-links', [PortfolioLinkController::class, 'store'])->name('portfolio-links.store');
    Route::put('/portfolio-links/{portfolioLink}', [PortfolioLinkController::class, 'update'])->name('portfolio-links.update');
    Route::delete('/portfolio-links/{portfolioLink}', [PortfolioLinkController::class, 'destroy'])->name('portfolio-links.destroy');

    // Module 2: Study Group Management
    Route::get('/groups', [StudyGroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [StudyGroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [StudyGroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}/edit', [StudyGroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{group}', [StudyGroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [StudyGroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('/groups/{group}/join', [StudyGroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [StudyGroupController::class, 'leave'])->name('groups.leave');

    // Module 2 & Module 3 Feature 1: Study Group Members Management & Join Requests
    Route::get('/groups/{group}/members', [StudyGroupMemberController::class, 'index'])->name('groups.members.index');
    Route::post('/groups/{group}/members/invite', [StudyGroupMemberController::class, 'invite'])->name('groups.members.invite');
    Route::patch('/groups/{group}/members/{member}/status', [StudyGroupMemberController::class, 'updateStatus'])->name('groups.members.updateStatus');
    Route::patch('/groups/{group}/members/{member}/role', [StudyGroupMemberController::class, 'updateRole'])->name('groups.members.updateRole');
    Route::delete('/groups/{group}/members/{member}', [StudyGroupMemberController::class, 'destroy'])->name('groups.members.destroy');

    // Module 3 Feature 2: Project Team Finder (Routes under /projects/recruitment and /projects)
    Route::controller(ProjectRecruitmentController::class)->group(function () {
        Route::get('/projects', 'index')->name('projects.index');
        Route::get('/projects/create', 'create')->name('projects.create');
        Route::post('/projects/recruitment', 'store')->name('projects.recruitment.store');
        Route::get('/projects/recruitment/{project}', 'show')->name('projects.show');
        Route::get('/projects/recruitment/{project}/edit', 'edit')->name('projects.edit');
        Route::put('/projects/recruitment/{project}', 'update')->name('projects.update_recruitment');
        Route::delete('/projects/recruitment/{project}', 'destroy')->name('projects.destroy_recruitment');

        // Project Join Request & Approval Endpoints
        Route::post('/projects/recruitment/{project}/request', 'requestJoin')->name('projects.request');
        Route::delete('/projects/recruitment/{project}/request', 'cancelRequest')->name('projects.cancelRequest');
        Route::patch('/projects/recruitment/{project}/requests/{member}/approve', 'approveRequest')->name('projects.requests.approve');
        Route::delete('/projects/recruitment/{project}/requests/{member}/reject', 'rejectRequest')->name('projects.requests.reject');
    });
});

// Auth routes are registered by Breeze in routes/auth.php
require __DIR__.'/auth.php';
