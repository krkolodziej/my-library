<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement([Book::STATUS_TO_READ, Book::STATUS_READING, Book::STATUS_READ]);
        
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(rand(2, 6)),
            'author' => fake()->name(),
            'isbn' => fake()->optional(0.7)->isbn13(),
            'description' => fake()->optional(0.8)->paragraphs(rand(2, 4), true),
            'pages' => fake()->optional(0.9)->numberBetween(50, 800),
            'published_year' => fake()->optional(0.8)->numberBetween(1950, 2024),
            'status' => $status,
            'rating' => $status === Book::STATUS_READ ? fake()->optional(0.7)->numberBetween(1, 5) : null,
            'notes' => fake()->optional(0.3)->paragraphs(rand(1, 3), true),
            'google_books_id' => fake()->optional(0.6)->uuid(),
            'cover_image' => fake()->optional(0.5)->imageUrl(300, 450, 'books', true),
            'started_reading_at' => $this->getStartedDate($status),
            'finished_reading_at' => $this->getFinishedDate($status),
        ];
    }

    
    public function toRead(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Book::STATUS_TO_READ,
            'rating' => null,
            'started_reading_at' => null,
            'finished_reading_at' => null,
        ]);
    }

    
    public function reading(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Book::STATUS_READING,
            'rating' => null,
            'started_reading_at' => \Carbon\Carbon::instance(fake()->dateTimeBetween('-3 months', 'now')),
            'finished_reading_at' => null,
        ]);
    }

    public function read(): static
    {
        $startedAt = \Carbon\Carbon::instance(fake()->dateTimeBetween('-2 years', '-1 week'));
        $finishedAt = \Carbon\Carbon::instance(fake()->dateTimeBetween($startedAt, 'now'));
        
        return $this->state(fn (array $attributes) => [
            'status' => Book::STATUS_READ,
            'rating' => fake()->optional(0.8)->numberBetween(1, 5),
            'started_reading_at' => $startedAt,
            'finished_reading_at' => $finishedAt,
        ]);
    }

  
    public function highRated(): static
    {
        return $this->read()->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
        ]);
    }

  
    public function withoutCover(): static
    {
        return $this->state(fn (array $attributes) => [
            'cover_image' => null,
        ]);
    }

    public function withGoogleId(): static
    {
        return $this->state(fn (array $attributes) => [
            'google_books_id' => 'gbs_' . fake()->uuid(),
        ]);
    }

    
    private function getStartedDate(string $status): ?\Carbon\Carbon
    {
        $date = match ($status) {
            Book::STATUS_READING => fake()->dateTimeBetween('-3 months', 'now'),
            Book::STATUS_READ => fake()->dateTimeBetween('-2 years', '-1 week'),
            default => null,
        };

        return $date ? \Carbon\Carbon::instance($date) : null;
    }

   
    private function getFinishedDate(string $status): ?\Carbon\Carbon
    {
        if ($status !== Book::STATUS_READ) {
            return null;
        }

        $startedAt = $this->getStartedDate($status);
        
        $finishedDate = fake()->dateTimeBetween($startedAt ?? '-2 years', 'now');

        return \Carbon\Carbon::instance($finishedDate);
    }
}