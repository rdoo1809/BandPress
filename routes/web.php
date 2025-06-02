<?php

use App\Http\Controllers\RepoController;
use App\Http\Controllers\SidebarController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [SidebarController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('builder', [SidebarController::class, 'builder'])
    ->middleware(['auth', 'verified'])
    ->name('builder');

Route::post('/create-repo', [RepoController::class, 'createUserRepo'])->name('create-repo');
Route::post('/new-event', [RepoController::class, 'createNewEvent'])->name('new-event');
Route::post('/new-release', [RepoController::class, 'createNewRelease'])->name('new-release');

Route::get('/hello', function () {
    return 'Hello, World!';
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
