# BandPress AI Agent Reference Guide

This document serves as a comprehensive reference for AI agents working with the BandPress codebase. It covers implementation patterns, architectural decisions, and development guidelines for both backend (Laravel) and frontend (Vue.js + Inertia) development.

## Table of Contents
1. [Application Overview](#application-overview)
2. [Backend Architecture (Laravel)](#backend-architecture-laravel)
3. [Frontend Architecture (Vue.js + Inertia)](#frontend-architecture-vuejs--inertia)
4. [Database Design & Patterns](#database-design--patterns)
5. [GitHub Integration Implementation](#github-integration-implementation)
6. [Testing Patterns](#testing-patterns)
7. [Code Organization & Conventions](#code-organization--conventions)
8. [Common Implementation Patterns](#common-implementation-patterns)
9. [Development Workflow](#development-workflow)

## Application Overview

**BandPress** is a website builder for local bands that enables musicians to manage their websites without coding knowledge. It currently supports one band (CityGround) but is architected for multi-band expansion.

**Key Features:**
- Automated website creation via GitHub repositories
- Event management with real-time website updates
- Release management with image uploads
- Logo/branding management
- GitHub Pages deployment automation

**Tech Stack:**
- Backend: Laravel 12.0, PHP 8.2+, SQLite
- Frontend: Vue.js 3.5.13, TypeScript, Inertia.js
- Styling: Tailwind CSS, Radix Vue components
- Deployment: GitHub API, GitHub Pages

## Backend Architecture (Laravel)

### Model Structure & Relationships

```php
// User Model - Standard Laravel authentication
class User extends Authenticatable {
    protected $fillable = ['name', 'email', 'password'];
    public function website(): HasOne { return $this->hasOne(BandSite::class); }
}

// BandSite Model - Links users to their websites
class BandSite extends Model {
    protected $fillable = ['repo_url', 'deployment_url', 'live_url'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function events(): HasMany { return $this->hasMany(BandEvent::class, 'band_site_id'); }
    public function releases(): HasMany { return $this->hasMany(BandRelease::class, 'band_site_id'); }
    public function siteContent(): HasOne { return $this->hasOne(SiteContent::class, 'site_id'); }
}

// BandEvent Model - Event data storage
class BandEvent extends Model {
    protected $fillable = ['band_site_id', 'name', 'day', 'month', 'description', 'venue_link'];
    public function bandSite(): BelongsTo { return $this->belongsTo(BandSite::class, 'band_site_id'); }
}

// BandRelease Model - Release data with image storage
class BandRelease extends Model {
    protected $fillable = ['band_site_id', 'cover_image', 'host_link'];
    public function bandSite(): BelongsTo { return $this->belongsTo(BandSite::class, 'band_site_id'); }
}

// SiteContent Model - Band-specific content like logos
class SiteContent extends Model {
    protected $fillable = ['site_id', 'logo'];
    public function site(): BelongsTo { return $this->belongsTo(BandSite::class, 'site_id', 'id'); }
}
```

### Controller Patterns

#### RepoController - GitHub Integration Hub
```php
class RepoController extends Controller {
    protected GitHubService $gitHubService;
    protected string $username;

    public function __construct(GitHubService $gitHubService) {
        $this->gitHubService = $gitHubService;
        $this->username = env('GITHUB_USERNAME');
    }

    // Repository creation from template
    public function createUserRepo(Request $request): JsonResponse {
        $user = Auth::user();
        $repoName = 'band-press-' . $user->email;

        $response = $this->gitHubService->createRepoFromTemplate($repoName, $this->username);

        if (isset($response['repo_url'])) {
            $user->website()->create([
                'repo_url' => $response['repo_url'],
                'deployment_url' => 'placeholder url',
            ]);
            return response()->json([...]);
        }
        return response()->json(['error' => 'GitHub repo creation failed'], 500);
    }

    // Event creation with GitHub sync
    public function createNewEvent(Request $request): JsonResponse {
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

        return response()->json(['event data' => $validated, 'component' => $component]);
    }
}
```

#### BuilderController - Content Management
```php
class BuilderController extends Controller {
    public function stashLogo(Request $request): JsonResponse {
        $request->validate(['logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);

        $user = auth()->user();
        $site = $user->website;

        $bandName = $user->name;
        $filename = Str::slug($bandName) . '_logo.' . $request->file('logo')->getClientOriginalExtension();
        $path = $request->file('logo')->storeAs('logos', $filename, 'public');

        $content = $site->siteContent()->firstOrCreate([]);
        $content->update(['logo' => $path]);

        return response()->json([
            'message' => 'Logo uploaded successfully.',
            'path' => asset("storage/{$path}"),
        ]);
    }
}
```

#### SidebarController - Page Rendering
```php
class SidebarController extends Controller {
    public function dashboard(): Response {
        $user = Auth::user();
        $hasWebsite = $user->website()->exists();

        return Inertia::render('Dashboard', [
            'hasWebsite' => $hasWebsite,
            'repoUrl' => $hasWebsite ? $user->website->repo_url : null,
            'liveUrl' => $hasWebsite ? $user->website->live_url : null,
            'dbEvents' => $hasWebsite ? $user->website->events : null,
            'dbReleases' => $hasWebsite ? $user->website->releases : null,
        ]);
    }
}
```

### GitHubService - Core Business Logic

The GitHubService handles all GitHub API interactions and file manipulations:

```php
class GitHubService {
    protected string $token;
    protected string $templateRepo;

    public function __construct() {
        $this->token = env('GITHUB_TOKEN');
        $this->templateRepo = env('GITHUB_TEMPLATE_REPO');
    }

    // Create repository from template
    public function createRepoFromTemplate(string $repoName, string $repoOwner): array {
        $response = Http::withToken($this->token)->post(
            "https://api.github.com/repos/{$repoOwner}/{$this->templateRepo}/generate",
            ['name' => $repoName, 'description' => "..."]
        );

        return $response->successful()
            ? ['repo_url' => $response->json()['html_url'], 'repo_name' => $response->json()['name']]
            : ['error' => 'Failed to create GitHub repository.', 'message' => $response->json()];
    }

    // Update Vue component with new event data
    public function addEventToDatesComponent(string $repoOwner, string $repoName, array $eventData): array {
        // 1. Fetch Pills.vue content
        $file = $this->getDatesComponent($repoOwner, 'City-Ground-BandPress');

        // 2. Parse and modify JavaScript events array
        $pattern = '/const events = ref\\(\\[([\\s\\S]*?)\\]\\)/';
        if (preg_match($pattern, $content, $matches)) {
            $existingEvents = $matches[1];
            $newEvent = "{\\n    title: \"{$eventData['name']}\",\\n    description: \"{$eventData['description']}\",\\n    month: \"{$eventData['month']}\",\\n    day: \"{$eventData['day']}\",\\n    link: \"{$eventData['venue_link']}\"\\n  }";

            $updatedEvents = empty(trim($existingEvents)) ? "\\n  " . $newEvent . "\\n" : $existingEvents . ",\\n  " . $newEvent . "\\n";
            $updatedContent = preg_replace($pattern, "const events = ref([\\n  " . $updatedEvents . "])", $content);
        }

        // 3. Commit updated file
        return $this->updateDatesComponent($repoOwner, 'City-Ground-BandPress', $updatedContent, $sha);
    }

    // Upload images to GitHub repository
    public function uploadImageToGitHub(string $repoOwner, string $repoName, string $localFilePath, string $githubFilePath): array {
        if (!file_exists($localFilePath)) return ['error' => 'File not found'];

        $imageContent = base64_encode(file_get_contents($localFilePath));
        $response = Http::withToken($this->token)->put(
            "https://api.github.com/repos/{$repoOwner}/City-Ground-BandPress/contents/{$githubFilePath}",
            [
                'message' => "Adding new release image: {$githubFilePath}",
                'content' => $imageContent,
                'branch' => 'main'
            ]
        );

        return $response->failed() ? ['error' => 'Failed to upload image'] : ['success' => true, 'path' => $githubFilePath];
    }
}
```

## Frontend Architecture (Vue.js + Inertia)

### Page Structure

#### Dashboard Page - Main Control Center
```vue
<script setup lang="ts">
import CreateSite from '@/components/CreateSite.vue';
import NewEventForm from '@/components/NewEventForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Events from '@/components/Events.vue';
import Releases from '@/components/Releases.vue';
import NewReleaseForm from '@/components/NewReleaseForm.vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

defineProps({
    hasWebsite: Boolean,
    repoUrl: String,
    liveUrl: String,
    dbEvents: Object,
    dbReleases: Object,
});
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div v-if="!hasWebsite" class="div-pattern flex items-center justify-center">
                    <CreateSite />
                </div>
                <div v-else class="div-pattern flex flex-col items-center justify-center">
                    <h1>Your site is live!</h1>
                    <a :href="liveUrl" target="_blank" class="hover:text-blue-600">{{ liveUrl }}</a>
                </div>
            </div>

            <div class="relative grid min-h-[100vh] flex-1 auto-rows-min gap-4 rounded-xl md:min-h-min md:grid-cols-3">
                <div class="full-length-div-pattern">
                    <NewEventForm />
                    <Events :database-events="dbEvents" />
                </div>
                <div class="full-length-div-pattern">
                    <NewReleaseForm />
                    <Releases :database-releases="dbReleases" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```

### Component Patterns

#### Form Components - Event Creation
```vue
<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const form = ref({
    name: '',
    day: '',
    month: '',
    description: '',
    venue_link: '',
});

const showForm = ref(false);
const toggleForm = () => { showForm.value = !showForm.value; };

const submitEvent = async () => {
    try {
        const response = await axios.post(route('new-event'), form.value);
        alert('Your Event has been added!');
        console.log(response.data);
        showForm.value = false;
    } catch (e) {
        console.log(e);
        alert(e);
    }
};
</script>

<template>
    <div class="max-w-lg mx-auto">
        <button @click="toggleForm" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-md">
            {{ showForm ? "Hide Form" : "Add Event" }}
        </button>

        <transition name="fade">
            <form v-if="showForm" @submit.prevent="submitEvent" class="mt-4 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-md space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                    <input v-model="form.name" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Event Name" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Day</label>
                        <input v-model="form.day" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="e.g., 15" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
                        <input v-model="form.month" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="e.g., March" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea v-model="form.description" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="Event details..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Venue Link</label>
                    <input v-model="form.venue_link" type="url" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="https://venue.com" />
                </div>

                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-700">
                        Submit Event
                    </button>
                </div>
            </form>
        </transition>
    </div>
</template>
```

#### Display Components - Event Listing
```vue
<script setup lang="ts">
defineProps({
    databaseEvents: Object,
});
</script>

<template>
    <div class="mt-4 rounded-xl bg-white p-4 shadow-md dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">City Ground Events</h2>
        <div class="max-h-96 space-y-3 overflow-y-auto pr-2">
            <div v-for="(event, index) in databaseEvents" :key="index" class="rounded-lg bg-gray-100 p-3 dark:bg-gray-700">
                <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">{{ event.name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ event.day }} - {{ event.month }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ event.description }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ event.venue_link }}</p>
            </div>
        </div>
    </div>
</template>
```

### Layout System

#### AppSidebarLayout - Main Application Layout
```vue
<script setup lang="ts">
import AppContent from '@/components/template/AppContent.vue';
import AppShell from '@/components/template/AppShell.vue';
import AppSidebar from '@/components/template/AppSidebar.vue';
import AppSidebarHeader from '@/components/template/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), { breadcrumbs: () => [] });
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </AppShell>
</template>
```

## Database Design & Patterns

### Migration Structure

#### Band Sites Table
```php
Schema::create('band_sites', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->string('repo_url');
    $table->string('deployment_url');
    $table->string('live_url')->nullable(); // Added later
    $table->timestamps();
});
```

#### Events Table
```php
Schema::create('band_events', function (Blueprint $table) {
    $table->id();
    $table->foreignId('band_site_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('day');
    $table->string('month');
    $table->text('description');
    $table->string('venue_link');
    $table->timestamps();
});
```

#### Releases Table
```php
Schema::create('band_releases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('band_site_id')->constrained()->onDelete('cascade');
    $table->string('host_link');
    $table->string('cover_image');
    $table->timestamps();
});
```

### Factory Patterns

#### UserFactory
```php
class UserFactory extends Factory {
    protected $model = User::class;

    public function definition(): array {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
```

#### BandSiteFactory
```php
class BandSiteFactory extends Factory {
    protected $model = BandSite::class;

    public function definition(): array {
        return [
            'user_id' => User::factory(),
            'repo_url' => $this->faker->url(),
            'deployment_url' => $this->faker->url(),
        ];
    }
}
```

## GitHub Integration Implementation

### Repository Creation Flow
1. User clicks "Create Website" → RepoController::createUserRepo()
2. Generate repo name: `band-press-{user-email}`
3. Call GitHub API: `POST /repos/{owner}/{template}/generate`
4. Store repo_url and deployment_url in BandSite model
5. Return success/error response

### Event Update Flow
1. User submits event form → RepoController::createNewEvent()
2. Validate and save to database
3. Call GitHubService::addEventToDatesComponent()
4. Fetch Pills.vue from GitHub repository
5. Parse JavaScript events array and add new event object
6. Commit updated file back to repository
7. GitHub Pages auto-deploys changes

### Release Update Flow
1. User submits release form → RepoController::createNewRelease()
2. Upload image to local storage
3. Save release data to database
4. Upload image to GitHub repository
5. Update CoverFlow.vue with new release component
6. Commit changes to repository

### Vue Component Modification Patterns

#### Event Addition (Pills.vue)
```javascript
// Original structure
const events = ref([
  {
    title: "Existing Event",
    description: "Event description",
    month: "March",
    day: "15",
    link: "https://venue.com"
  }
])

// After addition
const events = ref([
  {
    title: "Existing Event",
    description: "Event description",
    month: "March",
    day: "15",
    link: "https://venue.com"
  },
  {
    title: "New Event Name",
    description: "New event description",
    month: "April",
    day: "25",
    link: "https://newvenue.com"
  }
])
```

#### Release Addition (CoverFlow.vue)
```vue
<!-- Original structure -->
<ul>
  <Release image="/repo/images/existing.jpg" link="https://existing.com" />
</ul>

<!-- After addition -->
<ul>
  <Release image="/repo/images/existing.jpg" link="https://existing.com" />
  <Release image="/repo/images/new.jpg" link="https://newrelease.com" />
</ul>
```

## Testing Patterns

### Feature Tests

#### GitHubService Test
```php
class GitHubServiceTest extends TestCase {
    use HasFactory;

    protected GitHubService $gitHubService;

    protected function setUp(): void {
        parent::setUp();
        $this->gitHubService = new GitHubService();
        Storage::fake('public');
    }

    public function test_create_repo_from_template_success() {
        Http::fake([
            'https://api.github.com/repos/*' => Http::response([
                'html_url' => 'https://github.com/test-repo',
                'name' => 'test-repo'
            ], 201)
        ]);

        $result = $this->gitHubService->createRepoFromTemplate('test-repo', 'test-owner');

        $this->assertEquals([
            'repo_url' => 'https://github.com/test-repo',
            'repo_name' => 'test-repo',
        ], $result);
    }
}
```

#### RepoController Test
```php
class RepoControllerTest extends TestCase {
    use RefreshDatabase;

    protected GitHubService $gitHubService;

    protected function setUp(): void {
        parent::setUp();
        $this->gitHubService = $this->createMock(GitHubService::class);
        $this->app->instance(GitHubService::class, $this->gitHubService);
        Storage::fake('public');
    }

    public function test_create_user_repo_success() {
        $user = User::factory()->create();
        $this->actingAs($user);
        $repoName = 'band-press-' . $user->email;

        $this->gitHubService->method('createRepoFromTemplate')
            ->with($repoName, env('GITHUB_USERNAME'))
            ->willReturn(['repo_url' => "https://github.com/{$repoName}"]);

        $response = $this->postJson(route('create-repo'), []);

        $response->assertStatus(200)
            ->assertJson(['user' => ['id' => $user->id], 'repo' => $repoName]);
        $this->assertDatabaseHas('band_sites', [
            'user_id' => $user->id,
            'repo_url' => "https://github.com/{$repoName}",
        ]);
    }
}
```

## Code Organization & Conventions

### Directory Structure
```
app/
├── Http/Controllers/
│   ├── Auth/           # Authentication controllers
│   ├── BandSiteController.php
│   ├── BuilderController.php
│   ├── RepoController.php
│   └── SidebarController.php
├── Models/             # Eloquent models
├── Services/           # Business logic services
└── Providers/          # Service providers

database/
├── factories/          # Model factories
├── migrations/         # Database migrations
└── seeders/            # Database seeders

resources/
├── css/app.css         # Global styles
├── js/
│   ├── components/     # Vue components
│   ├── composables/    # Vue composables
│   ├── layouts/        # Page layouts
│   ├── lib/            # Utility libraries
│   ├── pages/          # Inertia pages
│   └── types/          # TypeScript types
└── views/              # Blade templates

routes/
├── web.php             # Web routes
├── auth.php            # Authentication routes
└── settings.php        # Settings routes
```

### Naming Conventions

#### PHP/Laravel
- **Classes**: PascalCase (User, BandSite, GitHubService)
- **Methods**: camelCase (createUserRepo, addEventToDatesComponent)
- **Variables**: camelCase ($gitHubService, $repoOwner)
- **Constants**: UPPER_SNAKE_CASE (GITHUB_TOKEN)
- **Routes**: kebab-case ('create-repo', 'new-event')

#### Vue/TypeScript
- **Components**: PascalCase (NewEventForm, AppLayout)
- **Files**: kebab-case (new-event-form.vue, app-layout.vue)
- **Variables**: camelCase (showForm, formData)
- **Props**: camelCase (databaseEvents, hasWebsite)
- **Events**: camelCase (submitEvent, toggleForm)

#### Database
- **Tables**: snake_case (band_sites, band_events)
- **Columns**: snake_case (repo_url, band_site_id)
- **Foreign Keys**: {parent_table}_id (user_id, band_site_id)

## Common Implementation Patterns

### Error Handling
```php
// Backend error handling
try {
    $response = $this->gitHubService->createRepoFromTemplate($repoName, $this->username);
    if (isset($response['repo_url'])) {
        // Success logic
        return response()->json(['success' => true]);
    } else {
        return response()->json([
            'error' => 'GitHub repo creation failed',
            'details' => $response['message'] ?? 'Unknown error'
        ], 500);
    }
} catch (Exception $e) {
    Log::error('GitHub repo creation failed', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Internal server error'], 500);
}
```

### File Upload Handling
```php
// Image upload with validation
public function createNewRelease(Request $request): JsonResponse {
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
}
```

### Form Validation
```php
// Request validation
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'day' => 'required|string|max:10',
    'month' => 'required|string|max:20',
    'description' => 'required|string',
    'venue_link' => 'required|url',
]);
```

### API Response Patterns
```php
// Consistent JSON responses
return response()->json([
    'event_data' => $validated,
    'component' => $component,
    'timestamp' => now(),
]);

return response()->json([
    'error' => 'No website found.',
    'code' => 'WEBSITE_NOT_FOUND'
], 404);
```

## Development Workflow

### Local Development Setup
```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start development servers
composer run dev  # Starts Laravel, queue, logs, and Vite
```

### Code Quality Tools
```bash
# PHP linting and formatting
./vendor/bin/pint

# JavaScript/TypeScript linting
npm run lint

# Testing
php artisan test
```

### Git Workflow
```bash
# Feature development
git checkout -b feature/new-functionality
# Make changes, write tests
git add .
git commit -m "feat: add new functionality"
git push origin feature/new-functionality

# Code review and merge
# Deploy via GitHub Actions or manual deployment
```

### Deployment Considerations
- Set environment variables (GITHUB_TOKEN, GITHUB_USERNAME, etc.)
- Configure database for production
- Set up proper file permissions
- Enable HTTPS
- Configure GitHub Pages deployment
- Set up monitoring and logging

This reference guide should provide comprehensive guidance for understanding and extending the BandPress application. Always refer to existing code patterns and maintain consistency with the established architecture.
