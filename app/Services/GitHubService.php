<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\error;

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

    public function createRepoFromTemplate($repoName, $repoOwner): array
    {
        $response = Http::withToken($this->token)->post("https://api.github.com/repos/{$repoOwner}/{$this->templateRepo}/generate", [
            'name' => $repoName,
            'description' => "New BandPress repo from " . $this->templateRepo . " for " . $repoName,
        ]);

        if ($response->successful()) {
            return [
                'repo_url' => $response->json()['html_url'],
                'repo_name' => $response->json()['name'],
            ];
        } else {
            return [
                'error' => 'Failed to create GitHub repository.',
                'message' => $response->json()
            ];
        }
    }

    public function addEventToDatesComponent($repoOwner, $repoName, $eventData): array
    {
        // Step 1: Fetch the Dates.vue content
        $file = $this->getDatesComponent($repoOwner, 'City-Ground-BandPress');
        $content = $file['content'];
        $sha = $file['sha'];

        // Step 2: Define the new component
        $newEvent = "<Event title=\"{$eventData['name']}\" description=\"{$eventData['description']}\"
  month=\"{$eventData['month']}\" day=\"{$eventData['day']}\" link=\"{$eventData['venue_link']}\" />\n";

        // Step 3: Insert before the closing </div> tag
        $pattern = '/(<\/div>)/';
        $replacement = "$newEvent$1";
        $updatedContent = preg_replace($pattern, $replacement, $content, 1);

        // Step 4: Commit and push the updated file
        return $this->updateDatesComponent($repoOwner, 'City-Ground-BandPress', $updatedContent, $sha);
    }

    public function getDatesComponent($repoOwner, $repoName, $filePath = 'src/components/Dates.vue')
    {
        $url = "https://api.github.com/repos/{$repoOwner}/{$repoName}/contents/{$filePath}";

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

    public function updateDatesComponent($repoOwner, $repoName, $updatedContent, $sha, $filePath = 'src/components/Dates.vue')
    {
        $url = "https://api.github.com/repos/{$repoOwner}/City-Ground-BandPress/contents/{$filePath}";

        $response = Http::withToken($this->token)->put($url, [
            'message' => 'Added new event to Dates.vue',
            'content' => base64_encode($updatedContent),
            'sha' => $sha,
        ]);

        if ($response->failed()) {
            return ['error' => 'Failed to update Dates.vue: ' . $response->body()];
        }

        return $response->json();
    }

    public function addReleaseToReleasesComponent($repoOwner, $repoName, $releaseData): array
    {
        //upload the release image to gh first
        $filename = basename($releaseData['cover_image']);
        $imagePath = storage_path("app/public/releases/{$filename}");
        $githubImagePath = "public/images/{$filename}"; // Destination in GitHub repo
        $imageUploadResponse = $this->uploadImageToGitHub($repoOwner, 'City-Ground-BandPress', $imagePath, $githubImagePath);

        // Step 1: Fetch the Dates.vue content
        $file = $this->getReleaseComponent($repoOwner, 'City-Ground-BandPress');
        $content = $file['content'];
        $sha = $file['sha'];

        // Step 2: Define the new <Release /> component
        $newRelease = "<Release image=\"/City-Ground-BandPress/images/{$filename}\" link=\"{$releaseData['host_link']}\" />\n";

        // Step 3: Insert before the closing </ul> tag
        $pattern = '/(<\/ul>)/';
        $replacement = "$newRelease$1";
        $updatedContent = preg_replace($pattern, $replacement, $content, 1);

        // Step 4: Commit and push the updated file
        return $this->updateCoverFlowComponent($repoOwner, 'City-Ground-BandPress', $updatedContent, $sha);
    }

    public function getReleaseComponent($repoOwner, $repoName, $filePath = 'src/components/CoverFlow.vue')
    {
        $url = "https://api.github.com/repos/{$repoOwner}/{$repoName}/contents/{$filePath}";

        $response = Http::withToken($this->token)->get($url);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch file: " . $response->body());
        }

        $fileData = $response->json();

        return [
            'content' => base64_decode($fileData['content']),
            'sha' => $fileData['sha'],
        ];
    }

    public function updateCoverFlowComponent($repoOwner, $repoName, $updatedContent, $sha, $filePath = 'src/components/CoverFlow.vue')
    {
        $url = "https://api.github.com/repos/{$repoOwner}/City-Ground-BandPress/contents/{$filePath}";

        $response = Http::withToken($this->token)->put($url, [
            'message' => 'Added new Release to CoverFlow.vue',
            'content' => base64_encode($updatedContent),
            'sha' => $sha,
        ]);

        if ($response->failed()) {
            return ['error' => 'Failed to update CoverFlow.vue: ' . $response->body()];
        }

        return $response->json();
    }

    public function uploadImageToGitHub($repoOwner, $repoName, $localFilePath, $githubFilePath)
    {
        $githubApiUrl = "https://api.github.com/repos/{$repoOwner}/City-Ground-BandPress/contents/{$githubFilePath}";

        if (!file_exists($localFilePath)) {
            return ['error' => 'File not found'];
        }

        $imageContent = base64_encode(file_get_contents($localFilePath));

        $response = Http::withToken($this->token)->put($githubApiUrl, [
            'message' => "Adding new release image: {$githubFilePath}",
            'content' => $imageContent,
            'branch' => 'main'
        ]);

        if ($response->failed()) {
            return ['error' => 'Failed to upload image: ' . $response->body()];
        }

        return ['success' => true, 'path' => $githubFilePath];
    }
}
