<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_reading_at' => 'date',
        'finished_reading_at' => 'date',
        'rating' => 'integer',
        'pages' => 'integer',
        'published_year' => 'integer',
    ];

   
    public const STATUS_TO_READ = 'to_read';
    public const STATUS_READING = 'reading';
    public const STATUS_READ = 'read';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_TO_READ => 'Do przeczytania',
            self::STATUS_READING => 'Czytam teraz',
            self::STATUS_READ => 'Przeczytana',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

   
    public function isRead(): bool
    {
        return $this->status === self::STATUS_READ;
    }

    
    public function isCurrentlyReading(): bool
    {
        return $this->status === self::STATUS_READING;
    }

    
    public function isToRead(): bool
    {
        return $this->status === self::STATUS_TO_READ;
    }

   
    public function markAsStarted(): self
    {
        $this->update([
            'status' => self::STATUS_READING,
            'started_reading_at' => now(),
        ]);

        return $this;
    }

   
    public function markAsFinished($rating = null): self
    {
        $data = [
            'status' => self::STATUS_READ,
            'finished_reading_at' => now(),
        ];

        if ($rating !== null) {
            $data['rating'] = $rating;
        }

        $this->update($data);

        return $this;
    }

   
    public function getStatusLabel(): string
    {
        return self::getStatuses()[$this->status] ?? 'Nieznany status';
    }

 
    public function getCoverUrl(): string
    {
        return $this->cover_image ?? '/images/book-placeholder.jpg';
    }

    public function getReadingDuration(): ?int
    {
        if ($this->started_reading_at && $this->finished_reading_at) {
            return $this->started_reading_at->diffInDays($this->finished_reading_at);
        }

        return null;
    }

 
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['status_label'] = $this->getStatusLabel();
        $array['cover_url'] = $this->getCoverUrl();
        $array['reading_duration'] = $this->getReadingDuration();
        
        return $array;
    }
}