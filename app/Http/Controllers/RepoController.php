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
        ]);

        $imageUrl = null;
        if ($request->hasFile('coverImage')) {
            $image = $request->file('coverImage');
            $imagePath = $image->store('releases', 'public');

            $imageUrl = asset('storage/' . $imagePath);
        }

        $newRelease = $website->releases()->create([
            'host_link' => $validated['hostLink'],
            'cover_image' => $imageUrl,
            'band_site_id' => $user->id
            ]);

        $component = $this->deployUpdatedRelease([
            'host_link' => $validated['hostLink'],
            'cover_image' => $imageUrl,
        ]);

        return response()->json([
            'new_release' => $newRelease,
            'component' => $component
        ]);
    }

    public function deployUpdatedRelease(array $releaseData): array
    {
        return $this->gitHubService->addReleaseToReleasesComponent('rdoo1809', 'City-Ground', $releaseData);
    }
}
