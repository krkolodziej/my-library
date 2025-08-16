<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function booksByStatus(string $status): HasMany
    {
        return $this->books()->where('status', $status);
    }

    
    public function readBooks(): HasMany
    {
        return $this->booksByStatus(Book::STATUS_READ);
    }

    public function currentlyReadingBooks(): HasMany
    {
        return $this->booksByStatus(Book::STATUS_READING);
    }

   
    public function toReadBooks(): HasMany
    {
        return $this->booksByStatus(Book::STATUS_TO_READ);
    }

    
    public function recentBooks(int $limit = 5): HasMany
    {
        return $this->books()->orderBy('created_at', 'desc')->limit($limit);
    }

    
    public function getLibraryStats(): array
    {
        return [
            'total_books' => $this->books()->count(),
            'read_books' => $this->readBooks()->count(),
            'currently_reading' => $this->currentlyReadingBooks()->count(),
            'to_read' => $this->toReadBooks()->count(),
        ];
    }

    public function hasBook(string $title, string $author = null): bool
    {
        $query = $this->books()->where('title', $title);
        
        if ($author) {
            $query->where('author', $author);
        }
        
        return $query->exists();
    }
}