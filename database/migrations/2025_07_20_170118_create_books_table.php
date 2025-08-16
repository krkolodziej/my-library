<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->string('author');
            $table->string('isbn')->nullable();
            $table->text('description')->nullable();
            $table->integer('pages')->nullable();
            $table->integer('published_year')->nullable();
            
            $table->enum('status', ['to_read', 'reading', 'read'])->default('to_read');
            $table->integer('rating')->nullable()->comment('Ocena 1-5');
            $table->text('notes')->nullable();
            
            $table->string('google_books_id')->nullable()->comment('ID z Google Books API');
            $table->string('cover_image')->nullable()->comment('URL do okładki');
            
            $table->date('started_reading_at')->nullable();
            $table->date('finished_reading_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index('google_books_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};