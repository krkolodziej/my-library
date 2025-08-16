<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Auth::user()->books();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['title', 'author', 'created_at', 'rating', 'published_year'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $books = $query->paginate(12)->withQueryString();

        return Inertia::render('Books/Index', [
            'books' => $books,
            'filters' => $request->only(['search', 'status', 'sort', 'direction']),
            'statuses' => Book::getStatuses(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Books/Create', [
            'statuses' => Book::getStatuses(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'pages' => 'nullable|integer|min:1|max:10000',
            'published_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'status' => ['required', Rule::in(array_keys(Book::getStatuses()))],
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|url|max:500',
        ]);

        $book = Auth::user()->books()->create($validated);

        if ($book->status === Book::STATUS_READING) {
            $book->markAsStarted();
        }

        if ($book->status === Book::STATUS_READ) {
            $book->update([
                'started_reading_at' => $request->started_reading_at ?? now()->subDays(7),
                'finished_reading_at' => now(),
            ]);
        }

        return redirect()->route('books.index')
            ->with('success', 'Książka została dodana do biblioteki.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): Response
    {
        $this->authorize('view', $book);

        return Inertia::render('Books/Show', [
            'book' => $book->load('user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book): Response
    {
        $this->authorize('update', $book);

        return Inertia::render('Books/Edit', [
            'book' => $book,
            'statuses' => Book::getStatuses(),
        ]);
    }

    
    public function update(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'pages' => 'nullable|integer|min:1|max:10000',
            'published_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'status' => ['required', Rule::in(array_keys(Book::getStatuses()))],
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|url|max:500',
            'started_reading_at' => 'nullable|date',
            'finished_reading_at' => 'nullable|date|after_or_equal:started_reading_at',
        ]);

        $oldStatus = $book->status;
        $newStatus = $validated['status'];

        if ($oldStatus !== $newStatus) {
            switch ($newStatus) {
                case Book::STATUS_READING:
                    if (!$book->started_reading_at) {
                        $validated['started_reading_at'] = now();
                    }
                    $validated['finished_reading_at'] = null;
                    break;

                case Book::STATUS_READ:
                    if (!$book->started_reading_at) {
                        $validated['started_reading_at'] = now()->subDays(7);
                    }
                    if (!$book->finished_reading_at) {
                        $validated['finished_reading_at'] = now();
                    }
                    break;

                case Book::STATUS_TO_READ:
                    $validated['started_reading_at'] = null;
                    $validated['finished_reading_at'] = null;
                    $validated['rating'] = null;
                    break;
            }
        }

        $book->update($validated);

        return redirect()->route('books.show', $book)
            ->with('success', 'Książka została zaktualizowana.');
    }

    
    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Książka została usunięta z biblioteki.');
    }

   
    public function updateStatus(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Book::getStatuses()))],
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $oldStatus = $book->status;
        $newStatus = $validated['status'];

        if ($oldStatus !== $newStatus) {
            switch ($newStatus) {
                case Book::STATUS_READING:
                    $book->markAsStarted();
                    break;

                case Book::STATUS_READ:
                    $book->markAsFinished($validated['rating'] ?? null);
                    break;

                case Book::STATUS_TO_READ:
                    $book->update([
                        'status' => $newStatus,
                        'started_reading_at' => null,
                        'finished_reading_at' => null,
                        'rating' => null,
                    ]);
                    break;
            }
        } elseif ($newStatus === Book::STATUS_READ && isset($validated['rating'])) {
            $book->update(['rating' => $validated['rating']]);
        }

        return back()->with('success', 'Status książki został zaktualizowany.');
    }

   
    public function stats()
    {
        $user = Auth::user();
        $stats = $user->getLibraryStats();
        $recentBooks = $user->recentBooks(5)->get();

        return response()->json([
            'stats' => [
                'totalBooks' => $stats['total_books'],
                'readBooks' => $stats['read_books'],
                'currentlyReading' => $stats['currently_reading'],
                'toRead' => $stats['to_read'],
            ],
            'recentBooks' => $recentBooks,
        ]);
    }
}