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

    public function addEventToDatesComponent($repoOwner, $repoName, $eventData): array
    {
        // Step 1: Fetch the Dates.vue content
//        $file = $this->getDatesComponent($repoOwner, $repoName);
        $file = $this->getDatesComponent('rdoo1809', 'City-Ground');
        $content = $file['content'];
        $sha = $file['sha'];

        // Step 2: Define the new <Event /> component
        $newEvent = "<Event title=\"{$eventData['name']}\" description=\"{$eventData['description']}\"
  month=\"{$eventData['month']}\" day=\"{$eventData['day']}\" link=\"{$eventData['venue_link']}\" />\n";

        // Step 3: Insert before the closing </div> tag
        $pattern = '/(<\/div>)/';
        $replacement = "$newEvent$1";
        $updatedContent = preg_replace($pattern, $replacement, $content, 1);

        // Step 4: Commit and push the updated file
        return $this->updateDatesComponent('rdoo1809', 'City-Ground', $updatedContent, $sha);
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
        $url = "https://api.github.com/repos/rdoo1809/City-Ground/contents/{$filePath}";

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
        $imageUploadResponse = $this->uploadImageToGitHub('rdoo1809', 'City-Ground', $imagePath, $githubImagePath);

        // Step 1: Fetch the Dates.vue content
        $file = $this->getReleaseComponent('rdoo1809', 'City-Ground');
        $content = $file['content'];
        $sha = $file['sha'];

        // Step 2: Define the new <Release /> component
        $newRelease = "<Release image=\"/City-Ground/images/{$filename}\" link=\"{$releaseData['host_link']}\" />\n";

        // Step 3: Insert before the closing </ul> tag
        $pattern = '/(<\/ul>)/';
        $replacement = "$newRelease$1";
        $updatedContent = preg_replace($pattern, $replacement, $content, 1);

        // Step 4: Commit and push the updated file
        return $this->updateCoverFlowComponent('rdoo1809', 'City-Ground', $updatedContent, $sha);
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
        $url = "https://api.github.com/repos/rdoo1809/City-Ground/contents/{$filePath}";

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
        $githubApiUrl = "https://api.github.com/repos/rdoo1809/City-Ground/contents/{$githubFilePath}";

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
