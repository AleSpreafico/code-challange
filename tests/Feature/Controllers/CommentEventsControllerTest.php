<?php


namespace Tests\Feature\Controllers;


use App\Mail\CommentAddedOnEvent;
use App\Models\Comment;
use App\Models\Events;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentEventsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanCreateCommentOnEvents(): void
    {
        Mail::fake();
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event = Events::factory()->make();
        $user1->events()->save($event);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->postJson(sprintf('/api/events/%s/comments', $event->id), [
            'content' => 'my awesome comment'
        ]);

        $response->assertOk();
    }

    public function testItCanDeleteCommentOnEvents(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event = Events::factory()->make();
        $user1->events()->save($event);

        $comment1 = Comment::factory()->make();
        $user1->comments()->save($comment1);
        $user1->events()->first()->comments()->save($comment1);

        Sanctum::actingAs(
            $user1,
            ['base-permission']
        );

        $response = $this->delete(sprintf('/api/comments/%s', $comment1->id));

        $response->assertOk();
    }

    public function testItCannotDeleteOtherUsersCommentOnNews(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event = Events::factory()->make();
        $user1->events()->save($event);

        $comment1 = Comment::factory()->make();
        $user1->comments()->save($comment1);
        $user1->events()->first()->comments()->save($comment1);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->delete(sprintf('/api/comments/%s', $comment1->id));

        $response->assertForbidden();
    }
}
