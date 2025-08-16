<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleBooksService
{
    private $baseUrl = 'https://www.googleapis.com/books/v1';
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_books.api_key');
    }

   
    public function searchBooks(string $query, int $maxResults = 20): array
    {
        try {
            $params = [
                'q' => $query,
                'maxResults' => min($maxResults, 40), 
                'printType' => 'books',
                'langRestrict' => 'pl,en', 
            ];

            if ($this->apiKey) {
                $params['key'] = $this->apiKey;
            }

            $response = Http::timeout(10)->get($this->baseUrl . '/volumes', $params);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatBooksData($data['items'] ?? []);
            }

            Log::warning('Google Books API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Google Books API exception', [
                'message' => $e->getMessage(),
                'query' => $query
            ]);

            return [];
        }
    }

   
    public function getBookById(string $googleBooksId): ?array
    {
        try {
            $params = [];
            if ($this->apiKey) {
                $params['key'] = $this->apiKey;
            }

            $response = Http::timeout(10)->get($this->baseUrl . '/volumes/' . $googleBooksId, $params);

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatSingleBook($data);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Google Books API get book exception', [
                'message' => $e->getMessage(),
                'id' => $googleBooksId
            ]);

            return null;
        }
    }

   
    private function formatBooksData(array $items): array
    {
        return array_map(function ($item) {
            return $this->formatSingleBook($item);
        }, $items);
    }

   
    private function formatSingleBook(array $item): array
    {
        $volumeInfo = $item['volumeInfo'] ?? [];
        $imageLinks = $volumeInfo['imageLinks'] ?? [];

        
        $authors = $volumeInfo['authors'] ?? [];
        $authorsString = !empty($authors) ? implode(', ', array_unique($authors)) : '';

       
        $isbn = $this->extractBestIsbn($volumeInfo['industryIdentifiers'] ?? []);

        
        $publishedYear = null;
        if (!empty($volumeInfo['publishedDate'])) {
            $publishedYear = (int) substr($volumeInfo['publishedDate'], 0, 4);
        }

        
        $coverUrl = $this->getBestCoverUrl($imageLinks);

        return [
            'google_books_id' => $item['id'] ?? '',
            'title' => $volumeInfo['title'] ?? '',
            'subtitle' => $volumeInfo['subtitle'] ?? '',
            'authors' => $authorsString,
            'description' => $this->cleanDescription($volumeInfo['description'] ?? ''),
            'isbn' => $isbn,
            'pages' => $volumeInfo['pageCount'] ?? null,
            'published_year' => $publishedYear,
            'publisher' => $volumeInfo['publisher'] ?? '',
            'language' => $volumeInfo['language'] ?? '',
            'cover_url' => $coverUrl,
            'categories' => $volumeInfo['categories'] ?? [],
            'average_rating' => $volumeInfo['averageRating'] ?? null,
            'ratings_count' => $volumeInfo['ratingsCount'] ?? null,
            'preview_link' => $volumeInfo['previewLink'] ?? '',
            'info_link' => $volumeInfo['infoLink'] ?? '',
        ];
    }

   
    private function extractBestIsbn(array $identifiers): string
    {
        $isbn13 = '';
        $isbn10 = '';

        foreach ($identifiers as $identifier) {
            $type = $identifier['type'] ?? '';
            $value = $identifier['identifier'] ?? '';

            if ($type === 'ISBN_13') {
                $isbn13 = $value;
            } elseif ($type === 'ISBN_10') {
                $isbn10 = $value;
            }
        }

        
        return $isbn13 ?: $isbn10;
    }

    
    private function getBestCoverUrl(array $imageLinks): string
    {
        
        $priorities = ['extraLarge', 'large', 'medium', 'small', 'thumbnail', 'smallThumbnail'];

        foreach ($priorities as $size) {
            if (!empty($imageLinks[$size])) {
                
                return str_replace('http://', 'https://', $imageLinks[$size]);
            }
        }

        return '';
    }

    
    private function cleanDescription(string $description): string
    {
        
        $cleaned = strip_tags($description);
        
        
        if (strlen($cleaned) > 2000) {
            $cleaned = substr($cleaned, 0, 1997) . '...';
        }

        return trim($cleaned);
    }

    
    public function searchByTitleAndAuthor(string $title, string $author = ''): array
    {
        $query = $title;
        
        if (!empty($author)) {
            $query .= ' inauthor:' . $author;
        }

        return $this->searchBooks($query, 10);
    }

    
    public function searchByIsbn(string $isbn): array
    {
        
        $cleanIsbn = preg_replace('/[^0-9X]/i', '', $isbn);
        
        return $this->searchBooks('isbn:' . $cleanIsbn, 5);
    }

    
    public function hasApiKey(): bool
    {
        return !empty($this->apiKey);
    }

    
    public function getApiInfo(): array
    {
        return [
            'has_api_key' => $this->hasApiKey(),
            'daily_limit' => $this->hasApiKey() ? 'Unlimited*' : '1000 requests',
            'note' => $this->hasApiKey() ? 'Paid quota may apply' : 'Free tier'
        ];
    }
}