<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookModelInteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_belongs_to_user()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $book->user);
        $this->assertEquals($user->id, $book->user->id);
    }

    public function test_scope_for_user_filters_correctly()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $book1 = Book::factory()->create(['user_id' => $user1->id, 'title' => 'User 1 Book']);
        $book2 = Book::factory()->create(['user_id' => $user2->id, 'title' => 'User 2 Book']);
        $book3 = Book::factory()->create(['user_id' => $user1->id, 'title' => 'Another User 1 Book']);

        $user1Books = Book::forUser($user1->id)->get();
        
        $this->assertCount(2, $user1Books);
        $this->assertTrue($user1Books->contains('title', 'User 1 Book'));
        $this->assertTrue($user1Books->contains('title', 'Another User 1 Book'));
        $this->assertFalse($user1Books->contains('title', 'User 2 Book'));
    }

    public function test_scope_by_status_filters_correctly()
    {
        $user = User::factory()->create();
        
        Book::factory()->create(['user_id' => $user->id, 'status' => Book::STATUS_READ]);
        Book::factory()->create(['user_id' => $user->id, 'status' => Book::STATUS_READING]);
        Book::factory()->create(['user_id' => $user->id, 'status' => Book::STATUS_TO_READ]);
        Book::factory()->create(['user_id' => $user->id, 'status' => Book::STATUS_READ]);

        $readBooks = Book::byStatus(Book::STATUS_READ)->get();
        $readingBooks = Book::byStatus(Book::STATUS_READING)->get();
        $toReadBooks = Book::byStatus(Book::STATUS_TO_READ)->get();

        $this->assertCount(2, $readBooks);
        $this->assertCount(1, $readingBooks);
        $this->assertCount(1, $toReadBooks);
    }

    public function test_scope_recent_orders_by_created_at_desc()
    {
        $user = User::factory()->create();
        
        $oldBook = Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Book',
            'created_at' => Carbon::now()->subDays(10)
        ]);
        
        $newBook = Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'New Book',
            'created_at' => Carbon::now()
        ]);
        
        $mediumBook = Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'Medium Book',
            'created_at' => Carbon::now()->subDays(5)
        ]);

        $recentBooks = Book::recent(3)->get();
        
        $this->assertCount(3, $recentBooks);
        $this->assertEquals('New Book', $recentBooks[0]->title);
        $this->assertEquals('Medium Book', $recentBooks[1]->title);
        $this->assertEquals('Old Book', $recentBooks[2]->title);
    }

    public function test_scope_recent_limits_results()
    {
        $user = User::factory()->create();
        
        // Create 10 books
        for ($i = 1; $i <= 10; $i++) {
            Book::factory()->create([
                'user_id' => $user->id,
                'title' => "Book {$i}",
            ]);
        }

        $recentBooks = Book::recent(5)->get();
        
        $this->assertCount(5, $recentBooks);
    }

    public function test_mark_as_started_updates_status_and_date()
    {
        $book = Book::factory()->create([
            'status' => Book::STATUS_TO_READ,
            'started_reading_at' => null,
        ]);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $result = $book->markAsStarted();

        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals(Book::STATUS_READING, $book->fresh()->status);
        $this->assertEquals($now->toDateString(), $book->fresh()->started_reading_at->toDateString());
    }

    public function test_mark_as_finished_updates_status_and_date()
    {
        $book = Book::factory()->create([
            'status' => Book::STATUS_READING,
            'finished_reading_at' => null,
            'rating' => null,
        ]);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $result = $book->markAsFinished();

        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals(Book::STATUS_READ, $book->fresh()->status);
        $this->assertEquals($now->toDateString(), $book->fresh()->finished_reading_at->toDateString());
    }

    public function test_mark_as_finished_with_rating()
    {
        $book = Book::factory()->create([
            'status' => Book::STATUS_READING,
            'rating' => null,
        ]);

        $book->markAsFinished(5);

        $this->assertEquals(5, $book->fresh()->rating);
        $this->assertEquals(Book::STATUS_READ, $book->fresh()->status);
    }

    public function test_mark_as_finished_without_rating_preserves_existing()
    {
        $book = Book::factory()->create([
            'status' => Book::STATUS_READING,
            'rating' => 4,
        ]);

        $book->markAsFinished();

        $this->assertEquals(4, $book->fresh()->rating);
    }

    public function test_combined_scopes()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Book::factory()->create([
            'user_id' => $user1->id,
            'status' => Book::STATUS_READ,
            'title' => 'User 1 Read Book'
        ]);
        
        Book::factory()->create([
            'user_id' => $user2->id,
            'status' => Book::STATUS_READ,
            'title' => 'User 2 Read Book'
        ]);
        
        Book::factory()->create([
            'user_id' => $user1->id,
            'status' => Book::STATUS_READING,
            'title' => 'User 1 Reading Book'
        ]);

        $user1ReadBooks = Book::forUser($user1->id)->byStatus(Book::STATUS_READ)->get();
        
        $this->assertCount(1, $user1ReadBooks);
        $this->assertEquals('User 1 Read Book', $user1ReadBooks->first()->title);
    }

    public function test_date_casting_works_correctly()
    {
        $book = Book::factory()->create([
            'started_reading_at' => '2024-01-15',
            'finished_reading_at' => '2024-01-25',
        ]);

        $this->assertInstanceOf(Carbon::class, $book->started_reading_at);
        $this->assertInstanceOf(Carbon::class, $book->finished_reading_at);
        $this->assertEquals('2024-01-15', $book->started_reading_at->toDateString());
        $this->assertEquals('2024-01-25', $book->finished_reading_at->toDateString());
    }

    public function test_integer_casting_works_correctly()
    {
        $book = Book::factory()->create([
            'rating' => '4',
            'pages' => '250',
            'published_year' => '2024',
        ]);

        $this->assertIsInt($book->rating);
        $this->assertIsInt($book->pages);
        $this->assertIsInt($book->published_year);
        $this->assertEquals(4, $book->rating);
        $this->assertEquals(250, $book->pages);
        $this->assertEquals(2024, $book->published_year);
    }
}