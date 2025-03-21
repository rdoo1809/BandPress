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

    public function createRepoFromTemplate($repoName): array
    {
        $response = Http::withToken($this->token)->post("https://api.github.com/repos/{$this->username}/{$this->templateRepo}/generate", [
            'name' => $repoName,
            'description' => "New BandPress repo from " . $this->templateRepo . " for " . $repoName,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            Log::info('GitHub API Response:', $response->json()); // Log the raw response
            // Return only the relevant data from GitHub's response (repo URL, name, etc.)
            return [
                'repo_url' => $response->json()['html_url'],  // GitHub repo URL
                'repo_name' => $response->json()['name'],    // GitHub repo name
            ];
        } else {
            Log::info('RECEIVING A 500:', $response->json()); // Log the raw response
            // Return an error message if the request failed
            return [
                'error' => 'Failed to create GitHub repository.',
                'message' => $response->json()  // Optional: include more error details from GitHub
            ];
        }
    }

    public function getDatesComponent($repoOwner, $repoName, $filePath = 'src/components/Dates.vue')
    {
        $url = "https://api.github.com/repos/rdoo1809/City-Ground/contents/{$filePath}";
        //https://github.com/rdoo1809/City-Ground

        $response = Http::withToken($this->token)->get($url);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch file: " . $response->body());
        }

        $fileData = $response->json();

        return [
            'content' => base64_decode($fileData['content']), // Decode file content
            'sha' => $fileData['sha'], // Needed for updating the file
        ];
    }

//    public function addEventToDatesComponent($repoOwner, $repoName, $eventData): array
//    {
//        // Step 1: Fetch the Dates.vue content
//        $file = $this->getDatesComponent($repoOwner, $repoName);
//        $content = $file['content'];
//        $sha = $file['sha'];
//
//        return [
//            'content' => $content,
//        ];
//    }
}
