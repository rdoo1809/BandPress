<?php

namespace App\Http\Controllers;

use App\Models\BandSite;
use Illuminate\Http\Request;
use App\Services\GitHubService;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\Array_;

class RepoController
{
    protected $gitHubService;

    public function __construct(GitHubService $gitHubService)
    {
        $this->gitHubService = $gitHubService;
    }

    public function createUserRepo(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $repoName = 'band-press-' . $user->email;

        $response = $this->gitHubService->createRepoFromTemplate($repoName);

        if (isset($response['repo_url'])) {
            //Store repo details in DB (assuming you have a `website` table)
            $user->website()->create([
                'repo_url' => $response['repo_url'],
                'deployment_url' => 'placeholder url',
            ]);

            return response()->json([
                "user" => $user,
                "repo" => $repoName,
                "response" => $response
            ]);
        } else {
            return response()->json([
                'error' => 'GitHub repo creation failed',
                'details' => $response['message'] ?? 'Unknown error'
            ], 500);
        }
    }

    public function createNewEvent(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $website = $user->website;
        if (!$website) return response()->json(['error' => 'No website found.']);

        $validated = $request->validate([
            'name' => 'required|string',
            'day' => 'required|string',
            'month' => 'required|string',
            'description' => 'required|string',
            'venue_link' => 'required|string',
        ]);

        $website->events()->create($validated);
        $component = $this->deployUpdatedEvent($validated);

        return response()->json([
            'event data' => $validated,
            'component' => $component
        ]);
    }

    public function deployUpdatedEvent(array $eventData): array
    {
        return $this->gitHubService->addEventToDatesComponent('rdoo1809', 'City-Ground', $eventData);
    }

    public function createNewRelease(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $website = $user->website;
        if (!$website) return response()->json(['error' => 'No website found.']);

        $validated = $request->validate([
            'hostLink' => 'required|url',
            'coverImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'band_site_id' => 'required|exists:band_sites,id',
        ]);

        $website->releases()->create($validated);

        return response()->json([
            'website' => $website,
        ]);
    }
}
