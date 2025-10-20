# BandPress

BandPress is a comprehensive website builder designed specifically for local bands, enabling musicians to manage and update their websites without requiring any coding knowledge. The platform automates the entire website management process through seamless GitHub integration, providing a user-friendly interface for content management while handling all technical deployment automatically.

## Features

### 🎵 Event Management
- **Simple Form Interface**: Bands can add events through an intuitive form with fields for name, date, description, and venue links
- **Real-time Updates**: Changes are immediately committed to the band's GitHub repository and deployed via GitHub Pages
- **Calendar Display**: Events are automatically formatted and displayed on the band's website

### 🎸 Release Management
- **Cover Art Upload**: Support for album artwork upload with automatic image optimization
- **Streaming Links**: Add links to Spotify, Apple Music, Bandcamp, and other platforms
- **Gallery Display**: Releases are showcased in an attractive cover flow layout on the website

### 🎨 Branding & Customization
- **Logo Management**: Upload and manage band logos through a dedicated builder interface
- **Dark/Light Mode**: Built-in theme support with system preference detection
- **Responsive Design**: Mobile-first design that works beautifully on all devices

### 🚀 GitHub Integration
- **Automated Repository Creation**: Creates GitHub repositories from templates upon website creation
- **Real-time Deployment**: Automatic commits and pushes for all content changes
- **GitHub Pages Hosting**: Seamless deployment through GitHub's hosting platform

## Tech Stack

### Backend
- **Laravel 12.0**: PHP framework with MVC architecture
- **PHP 8.2+**: Server-side scripting with modern features
- **SQLite**: Lightweight database for development (easily configurable for production)
- **Inertia.js**: Modern monolith architecture for seamless SPA experience

### Frontend
- **Vue.js 3.5.13**: Progressive JavaScript framework with Composition API
- **TypeScript**: Type-safe development for better code quality
- **Tailwind CSS 3.4.1**: Utility-first CSS framework for rapid styling
- **Radix Vue**: Accessible UI components built on Radix UI primitives
- **Lucide Icons**: Beautiful, consistent icon library

### Development & Build Tools
- **Vite 6.2.0**: Fast build tool and development server
- **ESLint + Prettier**: Code linting and formatting for JavaScript/TypeScript
- **Laravel Pint**: PHP code formatting and style enforcement
- **Composer**: PHP dependency management
- **npm**: JavaScript package management

### External Integrations
- **GitHub API**: Repository management, file operations, and deployment
- **GitHub Pages**: Static site hosting and automatic deployment

## Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- Git
- GitHub account with personal access token

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/band-press.git
   cd band-press
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # Create SQLite database
   touch database/database.sqlite

   # Run migrations
   php artisan migrate

   # (Optional) Seed with sample data
   php artisan db:seed
   ```

6. **Configure environment variables**
   Edit `.env` file and add your GitHub credentials:
   ```env
   GITHUB_TOKEN=your_github_personal_access_token
   GITHUB_USERNAME=your_github_username
   GITHUB_TEMPLATE_REPO=your_template_repository_name
   ```

7. **Start the development servers**
   ```bash
   # Option 1: Start all services together
   composer run dev

   # Option 2: Start services individually
   php artisan serve                    # Laravel server on http://localhost:8000
   php artisan queue:listen --tries=1   # Queue worker for background jobs
   npm run dev                         # Vite dev server for frontend assets
   ```

8. **Access the application**
   Open your browser and navigate to `http://localhost:8000`

## Development Workflow

### Available Commands

```bash
# Development
composer run dev          # Start all development servers
npm run dev              # Start Vite dev server only
php artisan serve        # Start Laravel server only

# Code Quality
./vendor/bin/pint        # Format PHP code
npm run lint            # Lint and fix JavaScript/TypeScript
npm run format          # Format JavaScript/TypeScript code

# Testing
php artisan test         # Run PHP tests
npm run test            # Run JavaScript tests (if configured)

# Database
php artisan migrate      # Run database migrations
php artisan migrate:fresh --seed  # Reset and seed database

# Production Build
npm run build           # Build frontend assets for production
php artisan config:cache # Cache configuration for production
php artisan route:cache  # Cache routes for production
```

### Project Structure

```
band-press/
├── app/                    # Laravel application code
│   ├── Http/Controllers/   # HTTP controllers
│   ├── Models/            # Eloquent models
│   └── Services/          # Business logic services
├── database/              # Database migrations and seeds
│   ├── factories/         # Model factories
│   └── migrations/        # Database schema
├── public/                # Public assets
├── resources/             # Frontend resources
│   ├── css/              # Stylesheets
│   └── js/               # Vue.js application
│       ├── components/    # Vue components
│       ├── layouts/      # Page layouts
│       └── pages/        # Inertia pages
├── routes/                # Route definitions
├── storage/               # File storage
├── tests/                 # Test files
└── .cursorrules          # AI agent guidelines
```

## API Reference

### Authentication Endpoints
- `GET /login` - Login page
- `POST /login` - Authenticate user
- `POST /logout` - Logout user
- `GET /register` - Registration page
- `POST /register` - Create new account

### Main Application Endpoints
- `GET /dashboard` - Main dashboard with website management
- `GET /builder` - Logo and branding management
- `POST /create-repo` - Create new GitHub repository for website
- `POST /new-event` - Add new event (triggers GitHub deployment)
- `POST /new-release` - Add new release (triggers GitHub deployment)
- `POST /stash-logo` - Upload and store band logo

### Settings Endpoints
- `GET /settings/profile` - User profile management
- `PUT /settings/profile` - Update profile information
- `GET /settings/password` - Password change
- `PUT /settings/password` - Update password

## Architecture Overview

### Backend Architecture
BandPress follows a service-oriented architecture:

- **Controllers**: Handle HTTP requests and responses
- **Services**: Contain business logic (GitHub integration, file handling)
- **Models**: Eloquent ORM models with relationships
- **Middleware**: Authentication and authorization

### Frontend Architecture
The frontend uses Inertia.js for seamless SPA experience:

- **Pages**: Vue components rendered server-side via Inertia
- **Components**: Reusable Vue components with Composition API
- **Layouts**: Consistent page layouts with navigation
- **Composables**: Shared logic and state management

### GitHub Integration Flow

1. **Repository Creation**: User creates website → GitHub repository created from template
2. **Content Updates**: User adds event/release → Data saved to database → Vue components modified in GitHub → Auto-deployment via GitHub Pages
3. **File Management**: Images uploaded to both local storage and GitHub repository

## Testing

BandPress includes comprehensive test coverage:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/GitHubServiceTest.php

# Run with coverage
php artisan test --coverage
```

### Test Categories
- **Feature Tests**: API endpoint testing, user workflows
- **Unit Tests**: Individual service method testing
- **Integration Tests**: GitHub API interaction testing

## Deployment

### Production Setup

1. **Environment Configuration**
   ```bash
   # Set production environment variables
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql  # or postgresql
   ```

2. **Build Assets**
   ```bash
   npm run build
   ```

3. **Optimize Laravel**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Database Migration**
   ```bash
   php artisan migrate --force
   ```

### GitHub Pages Deployment

BandPress websites are automatically deployed via GitHub Pages when:
- Repository is created from template
- Content is updated (events/releases added)
- Changes are committed via the GitHub API

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Make your changes and write tests
4. Run the test suite: `php artisan test`
5. Format code: `./vendor/bin/pint && npm run format`
6. Commit your changes: `git commit -m 'feat: add new feature'`
7. Push to the branch: `git push origin feature/new-feature`
8. Submit a pull request

## Current Implementation Status

BandPress currently supports **CityGround** as its primary band, providing:

- ✅ Event submission and management
- ✅ Release upload with cover art
- ✅ Logo management and branding
- ✅ Automated GitHub repository management
- ✅ GitHub Pages deployment
- ✅ Responsive web design
- ✅ Dark/light theme support

## Future Enhancements

- 🔄 **Multi-band Support**: Extend platform to support multiple bands simultaneously
- 🎨 **Theme Customization**: Allow bands to customize website designs and layouts
- 📊 **Analytics Integration**: Add website traffic and engagement metrics
- 📱 **Mobile App**: Native mobile application for content management
- 🔒 **Advanced Permissions**: Role-based access control for band management teams
- 🌐 **Internationalization**: Multi-language support for global bands

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please create an issue in the GitHub repository or contact the development team.
