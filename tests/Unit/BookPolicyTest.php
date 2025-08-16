<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
use App\Policies\BookPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPolicyTest extends TestCase
{
    use RefreshDatabase;

    private BookPolicy $policy;
    private User $user;
    private User $otherUser;
    private Book $book;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->policy = new BookPolicy();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->book = Book::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_view_any_returns_true_for_any_user()
    {
        $this->assertTrue($this->policy->viewAny($this->user));
        $this->assertTrue($this->policy->viewAny($this->otherUser));
    }

    public function test_view_returns_true_for_book_owner()
    {
        $this->assertTrue($this->policy->view($this->user, $this->book));
    }

    public function test_view_returns_false_for_non_owner()
    {
        $this->assertFalse($this->policy->view($this->otherUser, $this->book));
    }

    public function test_create_returns_true_for_any_user()
    {
        $this->assertTrue($this->policy->create($this->user));
        $this->assertTrue($this->policy->create($this->otherUser));
    }

    public function test_update_returns_true_for_book_owner()
    {
        $this->assertTrue($this->policy->update($this->user, $this->book));
    }

    public function test_update_returns_false_for_non_owner()
    {
        $this->assertFalse($this->policy->update($this->otherUser, $this->book));
    }

    public function test_delete_returns_true_for_book_owner()
    {
        $this->assertTrue($this->policy->delete($this->user, $this->book));
    }

    public function test_delete_returns_false_for_non_owner()
    {
        $this->assertFalse($this->policy->delete($this->otherUser, $this->book));
    }

    public function test_restore_returns_true_for_book_owner()
    {
        $this->assertTrue($this->policy->restore($this->user, $this->book));
    }

    public function test_restore_returns_false_for_non_owner()
    {
        $this->assertFalse($this->policy->restore($this->otherUser, $this->book));
    }

    public function test_force_delete_returns_true_for_book_owner()
    {
        $this->assertTrue($this->policy->forceDelete($this->user, $this->book));
    }

    public function test_force_delete_returns_false_for_non_owner()
    {
        $this->assertFalse($this->policy->forceDelete($this->otherUser, $this->book));
    }

    public function test_all_policies_check_user_id_match()
    {
        $anotherUser = User::factory()->create();
        $anotherBook = Book::factory()->create(['user_id' => $anotherUser->id]);

        $this->assertFalse($this->policy->view($this->user, $anotherBook));
        $this->assertFalse($this->policy->update($this->user, $anotherBook));
        $this->assertFalse($this->policy->delete($this->user, $anotherBook));
        $this->assertFalse($this->policy->restore($this->user, $anotherBook));
        $this->assertFalse($this->policy->forceDelete($this->user, $anotherBook));

        $this->assertTrue($this->policy->view($anotherUser, $anotherBook));
        $this->assertTrue($this->policy->update($anotherUser, $anotherBook));
        $this->assertTrue($this->policy->delete($anotherUser, $anotherBook));
        $this->assertTrue($this->policy->restore($anotherUser, $anotherBook));
        $this->assertTrue($this->policy->forceDelete($anotherUser, $anotherBook));
    }

    public function test_policies_work_with_integer_user_ids()
    {
        $book = Book::factory()->create(['user_id' => (int) $this->user->id]);
        
        $this->assertTrue($this->policy->view($this->user, $book));
        $this->assertTrue($this->policy->update($this->user, $book));
        $this->assertTrue($this->policy->delete($this->user, $book));
        $this->assertTrue($this->policy->restore($this->user, $book));
        $this->assertTrue($this->policy->forceDelete($this->user, $book));
    }
}