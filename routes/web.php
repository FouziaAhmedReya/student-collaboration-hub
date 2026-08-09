<?php

use App\Http\Controllers\Modules\Fouzia\NoteController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notes');

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
