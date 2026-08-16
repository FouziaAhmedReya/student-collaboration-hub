<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PortfolioLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('profile.show');
});

Route::get('/dashboard', function () {
    return redirect()->route('profile.show');
})->middleware(['auth'])->name('dashboard');



Route::middleware(['auth'])->group(function () {
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

    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/portfolio-links', [PortfolioLinkController::class, 'store'])->name('portfolio-links.store');
    Route::put('/portfolio-links/{portfolioLink}', [PortfolioLinkController::class, 'update'])->name('portfolio-links.update');
    Route::delete('/portfolio-links/{portfolioLink}', [PortfolioLinkController::class, 'destroy'])->name('portfolio-links.destroy');
});

// Auth routes are registered by Breeze in routes/auth.php
require __DIR__.'/auth.php';
