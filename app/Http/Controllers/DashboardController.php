<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController
{
    public function index(): \Inertia\Response
    {
        $user = Auth::user();

        // Check if the user has a website record
        $hasWebsite = $user->website()->exists();

        // Fetch the repo_url only if the user has a website
        $repoUrl = $hasWebsite ? $user->website->repo_url : null;
        $liveUrl = $hasWebsite ? $user->website->live_url : null;
        $dbEvents = $hasWebsite ? $user->website->events : null;


        return Inertia::render('Dashboard', [
            'hasWebsite' => $hasWebsite,
            'repoUrl' => $repoUrl,
            'liveUrl' => $liveUrl,
            'dbEvents' => $dbEvents,
        ]);
    }
}
