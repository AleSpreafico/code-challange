<?php


namespace Tests\Feature\Controllers;


use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentNewsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanCreateCommentOnNews(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $response = $this->postJson('/api/events', [
            'title' => Factory::create()->title,
            'content' => Factory::create()->text,
            'valid_from' => Carbon::now()->addHour()->format(\DateTime::ATOM),
            'valid_to' => Carbon::now()->addDay()->format(\DateTime::ATOM),
            'gps_lat' => Factory::create()->latitude,
            'gps_lng' => Factory::create()->longitude,
        ]);

        $news1 = News::factory()->make();
        $user1->news()->save($news1);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->postJson(sprintf('/api/news/%s/comments', $news1->id), [
            'content' => 'my awesome comment'
        ]);

        $response->assertOk();
    }

    public function testItCanDeleteCommentOnNews(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $news1 = News::factory()->make();
        $user1->news()->save($news1);

        $comment1 = Comment::factory()->make();
        $user1->comments()->save($comment1);
        $user1->news()->first()->comments()->save($comment1);

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

        $news1 = News::factory()->make();
        $user1->news()->save($news1);

        $comment1 = Comment::factory()->make();
        $user1->comments()->save($comment1);
        $user1->news()->first()->comments()->save($comment1);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->delete(sprintf('/api/comments/%s', $comment1->id));

        $response->assertForbidden();
    }
}
