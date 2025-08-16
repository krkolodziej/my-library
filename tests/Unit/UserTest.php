<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_fillable_attributes()
    {
        $user = new User();
        
        $expected = [
            'name',
            'email',
            'password',
        ];
        
        $this->assertEquals($expected, $user->getFillable());
    }

    public function test_user_has_hidden_attributes()
    {
        $user = new User();
        
        $expected = [
            'password',
            'remember_token',
        ];
        
        $this->assertEquals($expected, $user->getHidden());
    }

    public function test_user_has_correct_casts()
    {
        $user = new User();
        
        $casts = $user->getCasts();
        
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertArrayHasKey('password', $casts);
        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('hashed', $casts['password']);
    }

    public function test_user_has_books_relationship()
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->books());
    }

    public function test_books_by_status_returns_correct_books()
    {
        $user = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READ,
            'title' => 'Read Book'
        ]);
        
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READING,
            'title' => 'Reading Book'
        ]);
        
        $readBooksRelation = $user->booksByStatus(Book::STATUS_READ);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $readBooksRelation);
        
        $readBooks = $readBooksRelation->get();
        $this->assertCount(1, $readBooks);
        $this->assertEquals('Read Book', $readBooks->first()->title);
    }

    public function test_read_books_relationship()
    {
        $user = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READ,
            'title' => 'Read Book'
        ]);
        
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READING,
            'title' => 'Reading Book'
        ]);
        
        $readBooks = $user->readBooks()->get();
        $this->assertCount(1, $readBooks);
        $this->assertEquals(Book::STATUS_READ, $readBooks->first()->status);
    }

    public function test_currently_reading_books_relationship()
    {
        $user = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READING,
            'title' => 'Reading Book'
        ]);
        
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READ,
            'title' => 'Read Book'
        ]);
        
        $readingBooks = $user->currentlyReadingBooks()->get();
        $this->assertCount(1, $readingBooks);
        $this->assertEquals(Book::STATUS_READING, $readingBooks->first()->status);
    }

    public function test_to_read_books_relationship()
    {
        $user = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_TO_READ,
            'title' => 'To Read Book'
        ]);
        
        Book::factory()->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READ,
            'title' => 'Read Book'
        ]);
        
        $toReadBooks = $user->toReadBooks()->get();
        $this->assertCount(1, $toReadBooks);
        $this->assertEquals(Book::STATUS_TO_READ, $toReadBooks->first()->status);
    }

    public function test_recent_books_returns_limited_results()
    {
        $user = User::factory()->create();
        
        // Create 10 books
        for ($i = 1; $i <= 10; $i++) {
            Book::factory()->create([
                'user_id' => $user->id,
                'title' => "Book {$i}",
                'created_at' => now()->subDays(10 - $i), // Newest first
            ]);
        }
        
        $recentBooks = $user->recentBooks(5)->get();
        $this->assertCount(5, $recentBooks);
        
        // Check that they are ordered by newest first
        $this->assertEquals('Book 10', $recentBooks->first()->title);
        $this->assertEquals('Book 6', $recentBooks->last()->title);
    }

    public function test_recent_books_uses_default_limit()
    {
        $user = User::factory()->create();
        
        // Create 10 books
        for ($i = 1; $i <= 10; $i++) {
            Book::factory()->create([
                'user_id' => $user->id,
                'title' => "Book {$i}",
            ]);
        }
        
        $recentBooks = $user->recentBooks()->get();
        $this->assertCount(5, $recentBooks); // Default limit is 5
    }

    public function test_get_library_stats_returns_correct_counts()
    {
        $user = User::factory()->create();
        
        // Create books with different statuses
        Book::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READ,
        ]);
        
        Book::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_READING,
        ]);
        
        Book::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => Book::STATUS_TO_READ,
        ]);
        
        $stats = $user->getLibraryStats();
        
        $expected = [
            'total_books' => 10,
            'read_books' => 3,
            'currently_reading' => 2,
            'to_read' => 5,
        ];
        
        $this->assertEquals($expected, $stats);
    }

    public function test_get_library_stats_returns_zeros_for_empty_library()
    {
        $user = User::factory()->create();
        
        $stats = $user->getLibraryStats();
        
        $expected = [
            'total_books' => 0,
            'read_books' => 0,
            'currently_reading' => 0,
            'to_read' => 0,
        ];
        
        $this->assertEquals($expected, $stats);
    }

    public function test_has_book_returns_true_when_user_has_book_by_title()
    {
        $user = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Book',
            'author' => 'Test Author',
        ]);
        
        $this->assertTrue($user->hasBook('Test Book'));
    }

    public function test_has_book_returns_false_when_user_does_not_have_book()
    {
        $user = User::factory()->create();
        
        $this->assertFalse($user->hasBook('Nonexistent Book'));
    }

    public function test_has_book_with_author_returns_true_when_both_match()
    {
        $user = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Book',
            'author' => 'Test Author',
        ]);
        
        $this->assertTrue($user->hasBook('Test Book', 'Test Author'));
    }

    public function test_has_book_with_author_returns_false_when_author_does_not_match()
    {
        $user = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Book',
            'author' => 'Test Author',
        ]);
        
        $this->assertFalse($user->hasBook('Test Book', 'Different Author'));
    }

    public function test_has_book_does_not_return_other_users_books()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user2->id,
            'title' => 'Test Book',
            'author' => 'Test Author',
        ]);
        
        $this->assertFalse($user1->hasBook('Test Book'));
    }
}