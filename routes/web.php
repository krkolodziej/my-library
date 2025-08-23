<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Temporary simple HTML response to test routing
    return response('
        <h1>Laravel Application Working</h1>
        <p>PHP Version: ' . PHP_VERSION . '</p>
        <p>Laravel Version: ' . Application::VERSION . '</p>
        <p>Time: ' . date('Y-m-d H:i:s') . '</p>
        <p><a href="/up">Health Check</a></p>
        <p><a href="/simple">Simple Test</a></p>
        <p><a href="/dashboard">Dashboard (requires login)</a></p>
    ', 200, ['Content-Type' => 'text/html']);
    
    // Original Inertia code - will restore after fixing routing
    /*
    try {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Inertia render failed',
            'message' => $e->getMessage(),
        ], 500);
    }
    */
});

// Simple health check (Laravel 12 uses /up by default)
Route::get('/health', function () {
    return 'OK - ' . date('Y-m-d H:i:s');
});

// Simple test without Inertia
Route::get('/simple', function () {
    return response()->json([
        'status' => 'Simple route works',
        'laravel' => Application::VERSION,
        'php' => PHP_VERSION,
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
});

// Debug route for production troubleshooting
Route::get('/debug-info', function () {
    try {
        return response()->json([
            'status' => 'working',
            'timestamp' => date('Y-m-d H:i:s'),
            'app_env' => env('APP_ENV', 'undefined'),
            'app_debug' => env('APP_DEBUG', 'undefined'),
            'app_key_set' => !empty(env('APP_KEY')),
            'db_connection' => env('DB_CONNECTION', 'undefined'),
            'php_version' => PHP_VERSION,
            'laravel_version' => Application::VERSION,
        ]);
    } catch (Exception $e) {
        return response('Error: ' . $e->getMessage(), 500);
    }
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    
    Route::resource('books', BookController::class);
    Route::patch('/books/{book}/status', [BookController::class, 'updateStatus'])->name('books.update-status');
    Route::get('/api/books/stats', [BookController::class, 'stats'])->name('books.stats');
    
    
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::post('/api/search/books', [SearchController::class, 'searchBooks'])->name('search.books');
    Route::post('/api/search/title-author', [SearchController::class, 'searchByTitleAuthor'])->name('search.title-author');
    Route::post('/api/search/isbn', [SearchController::class, 'searchByIsbn'])->name('search.isbn');
    Route::post('/api/search/details', [SearchController::class, 'getBookDetails'])->name('search.details');
    Route::post('/api/search/add-to-library', [SearchController::class, 'addToLibrary'])->name('search.add-to-library');
    Route::post('/api/search/preview', [SearchController::class, 'previewForForm'])->name('search.preview');
});

require __DIR__.'/auth.php';