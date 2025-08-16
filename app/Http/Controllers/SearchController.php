<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\GoogleBooksService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    private GoogleBooksService $googleBooksService;

    public function __construct(GoogleBooksService $googleBooksService)
    {
        $this->googleBooksService = $googleBooksService;
    }

   
    public function index(): Response
    {
        return Inertia::render('Search/Index', [
            'apiInfo' => $this->googleBooksService->getApiInfo()
        ]);
    }

 
    public function searchBooks(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
            'max_results' => 'nullable|integer|min:1|max:40'
        ]);

        $query = $request->get('query');
        $maxResults = $request->get('max_results', 20);

        $results = $this->googleBooksService->searchBooks($query, $maxResults);

        $userBookIds = $this->getUserBookGoogleIds();
        
        $results = array_map(function ($book) use ($userBookIds) {
            $book['in_library'] = in_array($book['google_books_id'], $userBookIds);
            return $book;
        }, $results);


        return response()->json([
            'books' => $results,
            'total' => count($results),
            'query' => $query
        ]);
    }

  
    public function searchByTitleAuthor(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|min:2|max:255',
            'author' => 'nullable|string|min:2|max:255'
        ]);

        $title = $request->get('title') ?? '';
        $author = $request->get('author') ?? '';

        if (empty($title) && empty($author)) {
            return response()->json([
                'error' => 'Podaj przynajmniej tytuł lub autora'
            ], 422);
        }

        $results = $this->googleBooksService->searchByTitleAndAuthor($title, $author);
        $userBookIds = $this->getUserBookGoogleIds();

        $results = array_map(function ($book) use ($userBookIds) {
            $book['in_library'] = in_array($book['google_books_id'], $userBookIds);
            return $book;
        }, $results);

        return response()->json([
            'books' => $results,
            'total' => count($results),
            'search_type' => 'title_author',
            'title' => $title,
            'author' => $author
        ]);
    }

    public function searchByIsbn(Request $request)
    {
        $request->validate([
            'isbn' => 'required|string|min:10|max:17'
        ]);

        $isbn = $request->get('isbn');
        $results = $this->googleBooksService->searchByIsbn($isbn);
        $userBookIds = $this->getUserBookGoogleIds();

        $results = array_map(function ($book) use ($userBookIds) {
            $book['in_library'] = in_array($book['google_books_id'], $userBookIds);
            return $book;
        }, $results);

        return response()->json([
            'books' => $results,
            'total' => count($results),
            'search_type' => 'isbn',
            'isbn' => $isbn
        ]);
    }

 
    public function getBookDetails(Request $request)
    {
        $request->validate([
            'google_books_id' => 'required|string'
        ]);

        $googleBooksId = $request->get('google_books_id');
        $book = $this->googleBooksService->getBookById($googleBooksId);

        if (!$book) {
            return response()->json([
                'error' => 'Nie znaleziono książki'
            ], 404);
        }

        $userBookIds = $this->getUserBookGoogleIds();
        $book['in_library'] = in_array($book['google_books_id'], $userBookIds);

        return response()->json(['book' => $book]);
    }


    public function addToLibrary(Request $request)
    {
        $request->validate([
            'google_books_id' => 'required|string',
            'status' => 'required|in:to_read,reading,read',
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string|max:2000'
        ]);

        $googleBooksId = $request->get('google_books_id');
        
        $existingBook = Auth::user()->books()
            ->where('google_books_id', $googleBooksId)
            ->first();

        if ($existingBook) {
            return response()->json([
                'error' => 'Ta książka jest już w Twojej bibliotece',
                'book_id' => $existingBook->id
            ], 422);
        }

        $googleBook = $this->googleBooksService->getBookById($googleBooksId);

        if (!$googleBook) {
            return response()->json([
                'error' => 'Nie można pobrać danych książki z Google Books'
            ], 404);
        }

        $bookData = [
            'title' => $googleBook['title'],
            'author' => $googleBook['authors'],
            'isbn' => $googleBook['isbn'],
            'description' => $googleBook['description'],
            'pages' => $googleBook['pages'],
            'published_year' => $googleBook['published_year'],
            'google_books_id' => $googleBook['google_books_id'],
            'cover_image' => $googleBook['cover_url'],
            'status' => $request->get('status'),
            'rating' => $request->get('rating'),
            'notes' => $request->get('notes')
        ];

        $book = Auth::user()->books()->create($bookData);

        switch ($book->status) {
            case Book::STATUS_READING:
                $book->markAsStarted();
                break;
            case Book::STATUS_READ:
                $book->update([
                    'started_reading_at' => now()->subDays(7),
                    'finished_reading_at' => now()
                ]);
                break;
        }

        return response()->json([
            'message' => 'Książka została dodana do biblioteki',
            'book' => $book->fresh()
        ]);
    }

   
    private function getUserBookGoogleIds(): array
    {
        return Auth::user()->books()
            ->whereNotNull('google_books_id')
            ->pluck('google_books_id')
            ->toArray();
    }

    
    public function previewForForm(Request $request)
    {
        $request->validate([
            'google_books_id' => 'required|string'
        ]);

        $googleBooksId = $request->get('google_books_id');
        $googleBook = $this->googleBooksService->getBookById($googleBooksId);

        if (!$googleBook) {
            return response()->json([
                'error' => 'Nie znaleziono książki'
            ], 404);
        }

        
        $formData = [
            'title' => $googleBook['title'],
            'author' => $googleBook['authors'],
            'isbn' => $googleBook['isbn'],
            'description' => $googleBook['description'],
            'pages' => $googleBook['pages'],
            'published_year' => $googleBook['published_year'],
            'cover_image' => $googleBook['cover_url'],
            'google_books_id' => $googleBook['google_books_id'],
            'status' => 'to_read', 
            'rating' => '',
            'notes' => ''
        ];

        return response()->json([
            'form_data' => $formData,
            'google_book' => $googleBook
        ]);
    }
}