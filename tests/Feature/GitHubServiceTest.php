<?php

namespace Tests\Feature;

use App\Services\GitHubService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GitHubServiceTest extends TestCase
{
    use HasFactory;

    protected $gitHubService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gitHubService = new GitHubService();
        Storage::fake('public');
    }

    public function test_create_repo_from_template_success()
    {
        // Arrange
        Http::fake([
            'https://api.github.com/repos/*' =>
                Http::response(['html_url' => 'https://github.com/test-repo', 'name' => 'test-repo'], 201),
        ]);

        // Act
        $result = $this->gitHubService->createRepoFromTemplate('test-repo', 'test-owner');

        // Assert
        $this->assertEquals([
            'repo_url' => 'https://github.com/test-repo',
            'repo_name' => 'test-repo',
        ], $result);
    }

    public function test_create_repo_from_template_failure()
    {
        // Arrange
        Http::fake([
            'https://api.github.com/repos/*' => Http::response(['message' => 'API error'], 400),
        ]);

        // Act
        $result = $this->gitHubService->createRepoFromTemplate('test-repo', 'test-owner');

        // Assert
        $this->assertEquals([
            'error' => 'Failed to create GitHub repository.',
            'message' => ['message' => 'API error'],
        ], $result);
    }

    public function test_add_event_to_dates_component_success()
    {
        // Arrange
        $eventData = ['name' => 'Test', 'day' => '25', 'month' => 'April', 'description' => 'Desc', 'venue_link' => 'https://example.com'];
        $originalContent = "<template><div></div></template>";
        Http::fake([
            'https://api.github.com/repos/*/City-Ground-BandPress/contents/src/components/Dates.vue' => Http::sequence()
                ->push(['content' => base64_encode($originalContent), 'sha' => 'abc123'], 200)
                ->push(['sha' => 'def456'], 200),
        ]);

        // Act
        $result = $this->gitHubService->addEventToDatesComponent('test-owner', 'City-Ground-BandPress', $eventData);

        // Assert
        $this->assertEquals(['sha' => 'def456'], $result);
    }

    public function test_upload_image_to_github_success()
    {
        // Arrange
        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('test.jpg');
        $filePath = Storage::disk('public')->putFileAs('releases', $fakeImage, 'test.jpg');
        $fullPath = Storage::disk('public')->path($filePath); // Use fake disk path
        Http::fake([
            'https://api.github.com/repos/*/City-Ground-BandPress/contents/*' => Http::response(['path' => 'public/images/test.jpg'], 201),
        ]);

        // Act
        $result = $this->gitHubService->uploadImageToGitHub('test-owner', 'City-Ground-BandPress', $fullPath, 'public/images/test.jpg');

        // Assert
        $this->assertEquals(['success' => true, 'path' => 'public/images/test.jpg'], $result);
    }

    public function test_upload_image_to_github_file_not_found()
    {
        // Act
        $result = $this->gitHubService->uploadImageToGitHub('test-owner', 'City-Ground-BandPress', '/nonexistent.jpg', 'public/images/test.jpg');

        // Assert
        $this->assertEquals(['error' => 'File not found'], $result);
    }

    public function test_add_release_to_releases_component_success()
    {
        // Arrange
        $releaseData = ['host_link' => 'https://example.com', 'cover_image' => 'releases/test.jpg'];
        $originalContent = "<template><ul></ul></template>";
        Http::fake([
            'https://api.github.com/repos/*/City-Ground-BandPress/contents/*' => Http::sequence()
                ->push(['content' => base64_encode($originalContent), 'sha' => 'abc123'], 200)
                ->push(['path' => 'public/images/test.jpg'], 201)
                ->push(['sha' => 'def456'], 200),
        ]);
        Storage::disk('public')->put('releases/test.jpg', 'test content');

        // Act
        $result = $this->gitHubService->addReleaseToReleasesComponent('test-owner', 'City-Ground-BandPress', $releaseData);

        // Assert
        $this->assertEquals(['path' => 'public/images/test.jpg'], $result);
    }
}
