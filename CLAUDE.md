# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 personal library management application that uses:
- **Backend**: Laravel 12 (PHP 8.2+) with Inertia.js for SPA functionality
- **Frontend**: Vue.js 3 with Tailwind CSS
- **Database**: SQLite (configured for development)
- **Authentication**: Laravel Breeze
- **External API**: Google Books API for book search and metadata

The application allows users to manage their personal book library, search for books via Google Books API, and track reading progress with statuses (to_read, reading, read).

## Development Commands

### Backend (Laravel/PHP)
```bash
# Start development server with all services
composer dev

# Individual services
php artisan serve                    # Web server (port 8000)
php artisan queue:listen --tries=1  # Queue worker
php artisan pail --timeout=0        # Log viewer

# Database
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed    # Fresh database with seeders
php artisan db:seed                 # Run seeders only

# Testing
composer test                       # Run all tests (clears config first)
php artisan test                    # Direct test command
php artisan test --filter=BookTest  # Run specific test

# Code quality
./vendor/bin/pint                   # Code formatting (Laravel Pint)
```

### Frontend (Vue.js/Node)
```bash
# Development
npm run dev                         # Start Vite dev server

# Production build
npm run build                       # Build for production (includes SSR)
```

### Combined Development
The `composer dev` command runs all services concurrently:
- Laravel server (blue)
- Queue worker (purple) 
- Log viewer (pink)
- Vite dev server (orange)

## Architecture & Key Components

### Models & Database
- **Book**: Core model with statuses (to_read, reading, read), Google Books integration
  - Key fields: title, author, isbn, google_books_id, status, rating, reading dates
  - Scopes: forUser(), byStatus(), recent()
  - Methods: markAsStarted(), markAsFinished(), status helpers
- **User**: Standard Laravel user with book relationship

### Controllers & Routes
- **BookController**: CRUD operations, status updates, statistics API
- **SearchController**: Google Books API integration with multiple search methods
- **ProfileController**: User profile management (Laravel Breeze)
- All authenticated routes are in middleware group, books use resource routes

### Services
- **GoogleBooksService**: Handles all Google Books API interactions
  - Methods: searchBooks(), getBookById(), searchByTitleAndAuthor(), searchByIsbn()
  - Formats API responses, handles ISBN extraction, cover image optimization
  - Configurable via `services.google_books.api_key` in config

### Frontend Structure
- **Inertia.js** for SPA behavior with Vue.js components
- **Pages**: Organized by feature (Books/, Auth/, Profile/, Search/)
- **Components**: Reusable UI components (Modals, Forms, etc.)
- **Layouts**: AuthenticatedLayout, GuestLayout

### Key Features
1. **Book Management**: Add, edit, delete books with reading status tracking
2. **Google Books Integration**: Search by title/author, ISBN, or general query
3. **Reading Progress**: Track start/finish dates, ratings, notes
4. **User Authentication**: Complete auth system via Laravel Breeze

## Configuration Notes
- SQLite database configured by default (database/database.sqlite)
- Google Books API key optional but recommended for higher rate limits
- Tailwind CSS with forms plugin for styling
- PHPUnit configured for feature and unit tests with in-memory SQLite

## Testing
- Feature tests cover authentication, book management, and API endpoints
- Uses in-memory SQLite for fast test execution
- Test database configuration in phpunit.xml