<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Services\GitHubService;
use Illuminate\Support\Facades\Auth;

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


////             Store repo details in DB (assuming you have a `websites` table)
////            $user->websites()->create([
////                'repo_name' => $repoName,
////                'repo_url' => $response['html_url'],
////            ]);
//
    }
}
