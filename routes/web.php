<?php

use App\Http\Controllers\RepoController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/create-repo', [RepoController::class, 'createUserRepo'])->name('create-repo');

Route::get('/hello', function () {
    return 'Hello, World!';
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
