<?php

use App\Http\Controllers\Modules\Fouzia\NoteController;
use App\Http\Controllers\Modules\Tuli\ProjectIdeaGeneratorController;
use App\Http\Controllers\Modules\Tuli\TeamRecommendationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\Sayeefa\TaskController;
 

// Sayeefa Module - Task Management API
Route::prefix('api')->group(function () {
    Route::post('/tasks', [TaskController::class, 'createTask']);
    Route::get('/projects/{projectId}/tasks', [TaskController::class, 'getProjectTasks']);
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::delete('/tasks/{id}', [TaskController::class, 'deleteTask']);
});
 

 
Route::get('/tasks-ui', function () {
    return view('tasks');
});
 
Route::redirect('/', '/notes');

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

// Tuli Module - Web Routes
Route::prefix('project-ideas')->name('project-ideas.')->group(function () {
    Route::get('/', [ProjectIdeaGeneratorController::class, 'index'])->name('index');
    Route::post('/generate', [ProjectIdeaGeneratorController::class, 'generate'])->name('generate');
});

Route::prefix('team-recommendations')->name('team-recommendations.')->group(function () {
    Route::get('/', [TeamRecommendationController::class, 'index'])->name('index');
    Route::post('/match', [TeamRecommendationController::class, 'match'])->name('match');
});

// Tuli Module - API Endpoints (matching tuli_saha backend specs)
Route::prefix('api')->group(function () {
    Route::get('/ideas', [ProjectIdeaGeneratorController::class, 'index']);
    Route::post('/ideas/generate', [ProjectIdeaGeneratorController::class, 'generate']);
    Route::get('/teammates', [TeamRecommendationController::class, 'index']);
    Route::post('/teams/match', [TeamRecommendationController::class, 'match']);
});
