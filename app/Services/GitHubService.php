<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubService
{
    protected $token;
    protected $username;
    protected $templateRepo;

    public function __construct()
    {
        $this->token = env('GITHUB_TOKEN');
        $this->username = env('GITHUB_USERNAME');
        $this->templateRepo = env('GITHUB_TEMPLATE_REPO');
    }

    public function createRepoFromTemplate($repoName)
    {
        $response = Http::withToken($this->token)->post("https://api.github.com/repos/{$this->username}/{$this->templateRepo}/generate", [
            'name' => $repoName,
            'description' => "New BandPress repo from " . $this->templateRepo . " for " . $repoName,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Return only the relevant data from GitHub's response (repo URL, name, etc.)
            return [
                'repo_url' => $response->json()['html_url'],  // GitHub repo URL
                'repo_name' => $response->json()['name'],    // GitHub repo name
            ];
        } else {
            // Return an error message if the request failed
            return [
                'error' => 'Failed to create GitHub repository.',
                'message' => $response->json()  // Optional: include more error details from GitHub
            ];
        }
    }
}
