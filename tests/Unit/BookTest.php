<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class BookTest extends TestCase
{
    public function test_book_has_fillable_attributes()
    {
        $book = new Book();
        
        $expected = [
            'user_id',
            'title',
            'author',
            'isbn',
            'description',
            'pages',
            'published_year',
            'status',
            'rating',
            'notes',
            'google_books_id',
            'cover_image',
            'started_reading_at',
            'finished_reading_at',
        ];
        
        $this->assertEquals($expected, $book->getFillable());
    }

    public function test_book_has_correct_casts()
    {
        $book = new Book();
        
        $casts = $book->getCasts();
        
        $this->assertEquals('date', $casts['started_reading_at']);
        $this->assertEquals('date', $casts['finished_reading_at']);
        $this->assertEquals('integer', $casts['rating']);
        $this->assertEquals('integer', $casts['pages']);
        $this->assertEquals('integer', $casts['published_year']);
    }

    public function test_get_statuses_returns_correct_array()
    {
        $expected = [
            Book::STATUS_TO_READ => 'Do przeczytania',
            Book::STATUS_READING => 'Czytam teraz',
            Book::STATUS_READ => 'Przeczytana',
        ];
        
        $this->assertEquals($expected, Book::getStatuses());
    }

    public function test_status_constants_have_correct_values()
    {
        $this->assertEquals('to_read', Book::STATUS_TO_READ);
        $this->assertEquals('reading', Book::STATUS_READING);
        $this->assertEquals('read', Book::STATUS_READ);
    }

    public function test_is_read_method()
    {
        $book = new Book();
        $book->status = Book::STATUS_READ;
        $this->assertTrue($book->isRead());
        
        $book->status = Book::STATUS_READING;
        $this->assertFalse($book->isRead());
        
        $book->status = Book::STATUS_TO_READ;
        $this->assertFalse($book->isRead());
    }

    public function test_is_currently_reading_method()
    {
        $book = new Book();
        $book->status = Book::STATUS_READING;
        $this->assertTrue($book->isCurrentlyReading());
        
        $book->status = Book::STATUS_READ;
        $this->assertFalse($book->isCurrentlyReading());
        
        $book->status = Book::STATUS_TO_READ;
        $this->assertFalse($book->isCurrentlyReading());
    }

    public function test_is_to_read_method()
    {
        $book = new Book();
        $book->status = Book::STATUS_TO_READ;
        $this->assertTrue($book->isToRead());
        
        $book->status = Book::STATUS_READING;
        $this->assertFalse($book->isToRead());
        
        $book->status = Book::STATUS_READ;
        $this->assertFalse($book->isToRead());
    }

    public function test_get_status_label_returns_correct_label()
    {
        $book = new Book();
        
        $book->status = Book::STATUS_TO_READ;
        $this->assertEquals('Do przeczytania', $book->getStatusLabel());
        
        $book->status = Book::STATUS_READING;
        $this->assertEquals('Czytam teraz', $book->getStatusLabel());
        
        $book->status = Book::STATUS_READ;
        $this->assertEquals('Przeczytana', $book->getStatusLabel());
        
        $book->status = 'invalid_status';
        $this->assertEquals('Nieznany status', $book->getStatusLabel());
    }

    public function test_get_cover_url_returns_placeholder_when_no_cover()
    {
        $book = new Book();
        $this->assertEquals('/images/book-placeholder.jpg', $book->getCoverUrl());
    }

    public function test_get_cover_url_returns_actual_url_when_set()
    {
        $book = new Book();
        $book->cover_image = 'https://example.com/cover.jpg';
        $this->assertEquals('https://example.com/cover.jpg', $book->getCoverUrl());
    }

    public function test_get_reading_duration_returns_null_when_dates_missing()
    {
        $book = new Book();
        $this->assertNull($book->getReadingDuration());
        
        $book->started_reading_at = Carbon::parse('2024-01-01');
        $this->assertNull($book->getReadingDuration());
        
        $book->started_reading_at = null;
        $book->finished_reading_at = Carbon::parse('2024-01-10');
        $this->assertNull($book->getReadingDuration());
    }

    public function test_get_reading_duration_calculates_correctly()
    {
        $book = new Book();
        $book->started_reading_at = Carbon::parse('2024-01-01');
        $book->finished_reading_at = Carbon::parse('2024-01-11');
        
        $this->assertEquals(10, $book->getReadingDuration());
    }

    public function test_to_array_includes_computed_attributes()
    {
        $book = new Book([
            'title' => 'Test Book',
            'status' => Book::STATUS_READING,
        ]);
        
        $array = $book->toArray();
        
        $this->assertArrayHasKey('status_label', $array);
        $this->assertArrayHasKey('cover_url', $array);
        $this->assertArrayHasKey('reading_duration', $array);
        
        $this->assertEquals('Czytam teraz', $array['status_label']);
        $this->assertEquals('/images/book-placeholder.jpg', $array['cover_url']);
        $this->assertNull($array['reading_duration']);
    }
}