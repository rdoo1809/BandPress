<?php

namespace Tests\Feature;

use App\Models\BandSite;
use App\Models\User;
use App\Services\GitHubService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RepoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $gitHubService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gitHubService = $this->createMock(GitHubService::class);
        $this->app->instance(GitHubService::class, $this->gitHubService);
        Storage::fake('public');
    }

    public function test_create_user_repo_success()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        $repoName = 'band-press-' . $user->email;
        $this->gitHubService->method('createRepoFromTemplate')
            ->with($repoName, env('GITHUB_USERNAME'))
            ->willReturn(['repo_url' => "https://github.com/{$repoName}"]);

        // Act
        $response = $this->postJson(route('create-repo'), []);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'user' => ['id' => $user->id],
                'repo' => $repoName,
                'response' => ['repo_url' => "https://github.com/{$repoName}"],
            ]);
        $this->assertDatabaseHas('band_sites', [
            'user_id' => $user->id,
            'repo_url' => "https://github.com/{$repoName}",
        ]);
    }

    public function test_create_user_repo_failure_no_arguments()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->gitHubService->method('createRepoFromTemplate')
            ->willReturn(['error' => 'Failed', 'message' => 'API error']);

        // Act
        $response = $this->postJson(route('create-repo'), []);

        // Assert
        $response->assertStatus(500)
            ->assertJson(['error' => 'GitHub repo creation failed']);
    }

    public function test_create_new_event_success()
    {
        // Arrange
        $user = User::factory()->has(BandSite::factory(), 'website')->create();
        $this->actingAs($user);
        $eventData = [
            'name' => 'Test Event',
            'day' => '25',
            'month' => 'April',
            'description' => 'Test description',
            'venue_link' => 'https://example.com',
        ];
        $this->gitHubService->method('addEventToDatesComponent')
            ->willReturn(['sha' => 'abc123']);

        // Act
        $response = $this->postJson(route('new-event'), $eventData);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'event data' => $eventData,
                'component' => ['sha' => 'abc123'],
            ]);
        $this->assertDatabaseHas('band_events', $eventData + ['band_site_id' => $user->website->id]);
    }

    public function test_create_new_event_no_website_errors()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act
        $response = $this->postJson(route('new-event'), [
            'name' => 'Test Event',
            'day' => '25',
            'month' => 'April',
            'description' => 'Test description',
            'venue_link' => 'https://example.com',
        ]);

        // Assert
        $response->assertJson(['error' => 'No website found.']);
    }

    public function test_create_new_release_success()
    {
        // Arrange
        $user = User::factory()->has(BandSite::factory(), 'website')->create();
        $this->actingAs($user);
        $file = UploadedFile::fake()->image('cover.jpg');
        $this->gitHubService->method('addReleaseToReleasesComponent')
            ->willReturn(['sha' => 'def456']);

        // Act
        $response = $this->postJson(route('new-release'), [
            'hostLink' => 'https://example.com',
            'coverImage' => $file,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'new_release' => ['host_link', 'cover_image'],
                'component' => ['sha'],
            ]);
        $this->assertDatabaseHas('band_releases', [
            'host_link' => 'https://example.com',
            'band_site_id' => $user->website->id,
        ]);
        Storage::disk('public')->assertExists('releases/' . $file->hashName());
    }

    public function test_create_new_release_no_image_fails()
    {
        // Arrange
        $user = User::factory()->has(BandSite::factory(), 'website')->create();
        $this->actingAs($user);
        $this->gitHubService->method('addReleaseToReleasesComponent')
            ->willReturn(['sha' => 'def456']);

        // Act
        $response = $this->postJson(route('new-release'), [
            'hostLink' => 'https://example.com',
        ]);

        // Assert
        $response->assertStatus(500);
    }
}
