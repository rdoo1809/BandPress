<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SidebarController
{
    public function dashboard(): \Inertia\Response
    {
        $user = Auth::user();

        $hasWebsite = $user->website()->exists();

        $repoUrl = $hasWebsite ? $user->website->repo_url : null;
        $liveUrl = $hasWebsite ? $user->website->live_url : null;
        $dbEvents = $hasWebsite ? $user->website->events : null;
        $dbReleases = $hasWebsite ? $user->website->releases : null;

        return Inertia::render('Dashboard', [
            'hasWebsite' => $hasWebsite,
            'repoUrl' => $repoUrl,
            'liveUrl' => $liveUrl,
            'dbEvents' => $dbEvents,
            'dbReleases' => $dbReleases,
        ]);
    }

    public function builder(): \Inertia\Response
    {
        $user = Auth::user();
        $bandName = $user->name;
        $contents = $user->website->siteContent;

        return Inertia::render('Builder', [
            'bandName' => $bandName,
            'contents' => $contents,
        ]);
    }
}
