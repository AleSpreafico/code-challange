<?php


namespace Tests\Feature\Controllers;


use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NewsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanReadAllNews(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $news1 = News::factory()->make();
        $news2 = News::factory()->make();
        $user1->news()->save($news1);
        $user1->news()->save($news2);
        $response = $this->get('/api/news');
        $response->assertOk();
        $response->assertJson([
            $news1->toArray(),
            $news2->toArray()
        ]);
    }

    public function testItCanReadOneNews(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $news1 = News::factory()->make();
        $news2 = News::factory()->make();
        $user1->news()->save($news1);
        $user1->news()->save($news2);

        $response = $this->get(sprintf('/api/news/%s', $news1->id));
        $response->assertOk();
        $response->assertJson($news1->toArray());
    }

    public function testItCanCreateNews(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->postJson('/api/news', [
            'title' => Factory::create()->title,
            'content' => Factory::create()->text
        ]);

        $response->assertOk();
    }

    public function testItCannotCreateNewsWithoutAuth(): void
    {
        $response = $this->postJson('/api/news', [
            'title' => Factory::create()->title,
            'content' => Factory::create()->text
        ]);

        $response->assertUnauthorized();
    }

    public function testItCanUpdateNews(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $news1 = News::factory()->make();
        $user1->news()->save($news1);

        Sanctum::actingAs(
            $user1,
            ['base-permission']
        );

        $response = $this->putJson(sprintf('/api/news/%s', $news1->id), [
            'title' => Factory::create()->words,
            'content' => Factory::create()->text
        ]);

        $response->assertOk();
    }

    public function testItCannotUpdateNewsByNotAuthorUser(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $news1 = News::factory()->make();
        $user1->news()->save($news1);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->putJson(sprintf('/api/news/%s', $news1->id), [
            'title' => Factory::create()->words,
            'content' => Factory::create()->text
        ]);

        $response->assertForbidden();
    }

    public function testItCanDeleteNews(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $news1 = News::factory()->make();
        $user1->news()->save($news1);

        Sanctum::actingAs(
            $user1,
            ['base-permission']
        );

        $response = $this->delete(sprintf('/api/news/%s', $news1->id));

        $response->assertOk();
    }

    public function testItCannotDeleteNewsByNotAuthorUser(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $news1 = News::factory()->make();
        $user1->news()->save($news1);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->delete(sprintf('/api/news/%s', $news1->id));

        $response->assertForbidden();
    }

    public function testItCannotDeleteNewsWhenThereAreComments(): void
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

        $response = $this->delete(sprintf('/api/news/%s', $news1->id));

        $response->assertForbidden();
    }
}
