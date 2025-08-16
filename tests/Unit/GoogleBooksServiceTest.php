<?php

namespace Tests\Unit;

use App\Services\GoogleBooksService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GoogleBooksServiceTest extends TestCase
{
    private GoogleBooksService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GoogleBooksService();
    }

    public function test_constructor_sets_api_key_from_config()
    {
        Config::set('services.google_books.api_key', 'test-api-key');
        
        $service = new GoogleBooksService();
        
        $this->assertTrue($service->hasApiKey());
    }

    public function test_has_api_key_returns_false_when_no_key()
    {
        Config::set('services.google_books.api_key', null);
        
        $service = new GoogleBooksService();
        
        $this->assertFalse($service->hasApiKey());
    }

    public function test_has_api_key_returns_true_when_key_exists()
    {
        Config::set('services.google_books.api_key', 'test-key');
        
        $service = new GoogleBooksService();
        
        $this->assertTrue($service->hasApiKey());
    }

    public function test_get_api_info_without_key()
    {
        Config::set('services.google_books.api_key', null);
        
        $service = new GoogleBooksService();
        $info = $service->getApiInfo();
        
        $expected = [
            'has_api_key' => false,
            'daily_limit' => '1000 requests',
            'note' => 'Free tier'
        ];
        
        $this->assertEquals($expected, $info);
    }

    public function test_get_api_info_with_key()
    {
        Config::set('services.google_books.api_key', 'test-key');
        
        $service = new GoogleBooksService();
        $info = $service->getApiInfo();
        
        $expected = [
            'has_api_key' => true,
            'daily_limit' => 'Unlimited*',
            'note' => 'Paid quota may apply'
        ];
        
        $this->assertEquals($expected, $info);
    }

    public function test_search_books_returns_formatted_results()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [
                    [
                        'id' => 'test-id',
                        'volumeInfo' => [
                            'title' => 'Test Book',
                            'authors' => ['Test Author'],
                            'description' => 'Test description',
                            'publishedDate' => '2024-01-01',
                            'pageCount' => 200,
                            'industryIdentifiers' => [
                                ['type' => 'ISBN_13', 'identifier' => '9781234567890']
                            ],
                            'imageLinks' => [
                                'thumbnail' => 'http://example.com/cover.jpg'
                            ]
                        ]
                    ]
                ]
            ])
        ]);
        
        $result = $this->service->searchBooks('test query');
        
        $this->assertCount(1, $result);
        $this->assertEquals('Test Book', $result[0]['title']);
        $this->assertEquals('Test Author', $result[0]['authors']);
        $this->assertEquals('9781234567890', $result[0]['isbn']);
        $this->assertEquals('https://example.com/cover.jpg', $result[0]['cover_url']);
    }

    public function test_search_books_without_api_key()
    {
        Config::set('services.google_books.api_key', null);
        
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => []
            ])
        ]);
        
        $service = new GoogleBooksService();
        $result = $service->searchBooks('test query');
        
        Http::assertSentCount(1);
    }

    public function test_search_books_limits_max_results()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []])
        ]);
        
        $this->service->searchBooks('test', 50);
        
        Http::assertSentCount(1);
    }

    public function test_search_books_handles_api_error()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([], 500)
        ]);
        
        Log::shouldReceive('warning')->once();
        
        $result = $this->service->searchBooks('test');
        
        $this->assertEquals([], $result);
    }

    public function test_search_books_handles_exception()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => function () {
                throw new \Exception('Network error');
            }
        ]);
        
        Log::shouldReceive('error')->once();
        
        $result = $this->service->searchBooks('test');
        
        $this->assertEquals([], $result);
    }

    public function test_get_book_by_id_returns_formatted_result()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes/test-id*' => Http::response([
                'id' => 'test-id',
                'volumeInfo' => [
                    'title' => 'Test Book',
                    'authors' => ['Test Author']
                ]
            ])
        ]);
        
        $result = $this->service->getBookById('test-id');
        
        Http::assertSentCount(1);
        
        $this->assertNotNull($result);
        $this->assertEquals('Test Book', $result['title']);
        $this->assertEquals('Test Author', $result['authors']);
    }

    public function test_get_book_by_id_returns_null_on_error()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes/*' => Http::response([], 404)
        ]);
        
        $result = $this->service->getBookById('invalid-id');
        
        $this->assertNull($result);
    }

    public function test_search_by_title_and_author_calls_search_books()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []])
        ]);
        
        $result = $this->service->searchByTitleAndAuthor('Test Title', 'Test Author');
        
        Http::assertSentCount(1);
        $this->assertEquals([], $result);
    }

    public function test_search_by_title_and_author_without_author()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []])
        ]);
        
        $result = $this->service->searchByTitleAndAuthor('Test Title');
        
        Http::assertSentCount(1);
        $this->assertEquals([], $result);
    }

    public function test_search_by_isbn_formats_query_correctly()
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response(['items' => []])
        ]);
        
        $this->service->searchByIsbn('978-1-234-56789-0');
        
        Http::assertSentCount(1);
    }

    public function test_format_single_book_handles_all_fields()
    {
        $mockItem = [
            'id' => 'test-id',
            'volumeInfo' => [
                'title' => 'Test Book',
                'subtitle' => 'Test Subtitle',
                'authors' => ['Author 1', 'Author 2'],
                'description' => '<p>Test description with <b>HTML</b> tags</p>',
                'pageCount' => 300,
                'publishedDate' => '2024-01-15',
                'publisher' => 'Test Publisher',
                'language' => 'en',
                'categories' => ['Fiction', 'Mystery'],
                'averageRating' => 4.5,
                'ratingsCount' => 100,
                'previewLink' => 'https://books.google.com/preview',
                'infoLink' => 'https://books.google.com/info',
                'industryIdentifiers' => [
                    ['type' => 'ISBN_13', 'identifier' => '9781234567890'],
                    ['type' => 'ISBN_10', 'identifier' => '1234567890']
                ],
                'imageLinks' => [
                    'large' => 'http://example.com/large.jpg',
                    'thumbnail' => 'http://example.com/thumb.jpg'
                ]
            ]
        ];
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatSingleBook');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $mockItem);
        
        $this->assertEquals('test-id', $result['google_books_id']);
        $this->assertEquals('Test Book', $result['title']);
        $this->assertEquals('Test Subtitle', $result['subtitle']);
        $this->assertEquals('Author 1, Author 2', $result['authors']);
        $this->assertEquals('Test description with HTML tags', $result['description']);
        $this->assertEquals('9781234567890', $result['isbn']);
        $this->assertEquals(300, $result['pages']);
        $this->assertEquals(2024, $result['published_year']);
        $this->assertEquals('Test Publisher', $result['publisher']);
        $this->assertEquals('en', $result['language']);
        $this->assertEquals('https://example.com/large.jpg', $result['cover_url']);
        $this->assertEquals(['Fiction', 'Mystery'], $result['categories']);
        $this->assertEquals(4.5, $result['average_rating']);
        $this->assertEquals(100, $result['ratings_count']);
    }

    public function test_format_single_book_handles_missing_fields()
    {
        $mockItem = [
            'id' => 'test-id',
            'volumeInfo' => [
                'title' => 'Test Book'
            ]
        ];
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('formatSingleBook');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $mockItem);
        
        $this->assertEquals('test-id', $result['google_books_id']);
        $this->assertEquals('Test Book', $result['title']);
        $this->assertEquals('', $result['subtitle']);
        $this->assertEquals('', $result['authors']);
        $this->assertEquals('', $result['description']);
        $this->assertEquals('', $result['isbn']);
        $this->assertNull($result['pages']);
        $this->assertNull($result['published_year']);
        $this->assertEquals('', $result['cover_url']);
    }

    public function test_extract_best_isbn_prefers_isbn13()
    {
        $identifiers = [
            ['type' => 'ISBN_10', 'identifier' => '1234567890'],
            ['type' => 'ISBN_13', 'identifier' => '9781234567890']
        ];
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('extractBestIsbn');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $identifiers);
        
        $this->assertEquals('9781234567890', $result);
    }

    public function test_extract_best_isbn_falls_back_to_isbn10()
    {
        $identifiers = [
            ['type' => 'ISBN_10', 'identifier' => '1234567890']
        ];
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('extractBestIsbn');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $identifiers);
        
        $this->assertEquals('1234567890', $result);
    }

    public function test_get_best_cover_url_prioritizes_larger_images()
    {
        $imageLinks = [
            'thumbnail' => 'http://example.com/thumb.jpg',
            'large' => 'http://example.com/large.jpg',
            'medium' => 'http://example.com/medium.jpg'
        ];
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getBestCoverUrl');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $imageLinks);
        
        $this->assertEquals('https://example.com/large.jpg', $result);
    }

    public function test_get_best_cover_url_converts_http_to_https()
    {
        $imageLinks = [
            'thumbnail' => 'http://example.com/thumb.jpg'
        ];
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getBestCoverUrl');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $imageLinks);
        
        $this->assertEquals('https://example.com/thumb.jpg', $result);
    }

    public function test_clean_description_removes_html_tags()
    {
        $description = '<p>Test <b>description</b> with <i>HTML</i> tags</p>';
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('cleanDescription');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $description);
        
        $this->assertEquals('Test description with HTML tags', $result);
    }

    public function test_clean_description_truncates_long_text()
    {
        $description = str_repeat('A', 2500);
        
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('cleanDescription');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->service, $description);
        
        $this->assertEquals(2000, strlen($result));
        $this->assertStringEndsWith('...', $result);
    }
}