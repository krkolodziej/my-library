<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        if (User::count() === 0) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        } else {
           
            $user = User::first();
        }

        $this->createSampleBooks($user);
        
        Book::factory()
            ->count(15)
            ->for($user)
            ->create();

        $this->command->info('Utworzono przykładowe książki dla użytkownika: ' . $user->email);
    }

   
    private function createSampleBooks(User $user): void
    {
        $sampleBooks = [
            [
                'title' => 'Wiedźmin: Ostatnie życzenie',
                'author' => 'Andrzej Sapkowski',
                'isbn' => '9788375780627',
                'description' => 'Zbiór opowiadań o wiedźminie Geralcie z Rivii.',
                'pages' => 332,
                'published_year' => 1993,
                'status' => Book::STATUS_READ,
                'rating' => 5,
                'notes' => 'Fantastyczna książka! Świetny początek serii.',
            ],
            [
                'title' => 'Sapiens: Krótka historia ludzkości',
                'author' => 'Yuval Noah Harari',
                'isbn' => '9788324020775',
                'description' => 'Opowieść o tym, jak gatunek Homo sapiens zdołał zdominować Ziemię.',
                'pages' => 512,
                'published_year' => 2011,
                'status' => Book::STATUS_READING,
                'rating' => null,
                'notes' => 'Bardzo ciekawa perspektywa na historię ludzkości.',
                'started_reading_at' => now()->subWeeks(2),
            ],
            [
                'title' => 'Atomowe nawyki',
                'author' => 'James Clear',
                'isbn' => '9788381106109',
                'description' => 'Łatwy i sprawdzony sposób na dobre nawyki i pozbycie się złych.',
                'pages' => 320,
                'published_year' => 2018,
                'status' => Book::STATUS_TO_READ,
                'rating' => null,
                'notes' => 'Polecane przez znajomych.',
            ],
            [
                'title' => 'Sto lat samotności',
                'author' => 'Gabriel García Márquez',
                'isbn' => '9788324029969',
                'description' => 'Historia rodu Buendía w fikcyjnym miasteczku Macondo.',
                'pages' => 432,
                'published_year' => 1967,
                'status' => Book::STATUS_READ,
                'rating' => 4,
                'notes' => 'Klasyk literatury latynoamerykańskiej. Nieco trudny w czytaniu.',
                'started_reading_at' => now()->subMonths(3),
                'finished_reading_at' => now()->subMonths(2),
            ],
            [
                'title' => 'Diuna',
                'author' => 'Frank Herbert',
                'isbn' => '9788375780842',
                'description' => 'Epicki science fiction o planecie Arrakis i spice.',
                'pages' => 688,
                'published_year' => 1965,
                'status' => Book::STATUS_READING,
                'rating' => null,
                'notes' => 'Bardzo szczegółowy worldbuilding, ale wciągające.',
                'started_reading_at' => now()->subDays(5),
            ],
        ];

        foreach ($sampleBooks as $bookData) {
            Book::factory()
                ->for($user)
                ->create($bookData);
        }
    }
}