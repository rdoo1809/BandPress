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


        return [
            "answer" => "yoyoyo"
        ];



//        $response = Http::withToken($this->token)->post("https://api.github.com/repos/{$this->username}/{$this->templateRepo}/generate", [
//            'owner' => $this->username,
//            'name' => $repoName,
//            'description' => "New BandPress repo from ".$this->templateRepo." for ".$repoName,
//            'private' => false,
//        ]);
//
//        // Log the full response for debugging
//        Log::info('GitHub Response:', $response->json());
//
//        if ($response->successful()) {
//            return $response->json();
//        } else {
//            Log::error('GitHub API Error:', $response->json());
//            return null;
//        }
    }
}
