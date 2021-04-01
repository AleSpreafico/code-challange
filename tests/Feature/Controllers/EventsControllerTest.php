<?php


namespace Tests\Feature\Controllers;


use App\Models\Events;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventsControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanReadAllEvents(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event1 = Events::factory([
            'valid_from' => Carbon::today()->format(\DateTime::ATOM),
            'valid_to' => Carbon::tomorrow()->format(\DateTime::ATOM)
        ])->make();
        $user1->news()->save($event1);

        Sanctum::actingAs(
            $user1,
            ['base-permission']
        );

        $response = $this->get('/api/events');

        $response->assertOk();
        $response->assertJson([
            $event1->toArray()
        ]);
    }

    public function testItCanReadAllEventsFilteredByDate(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event1 = Events::factory([
            'valid_from' => Carbon::yesterday()->format(\DateTime::ATOM),
            'valid_to' => Carbon::tomorrow()->format(\DateTime::ATOM)
        ])->make();
        $event2 = Events::factory([
            'valid_from' => Carbon::today()->addDays(5)->format(\DateTime::ATOM),
            'valid_to' => Carbon::tomorrow()->addDays(6)->format(\DateTime::ATOM)
        ])->make();

        $user1->news()->save($event1);
        $user1->news()->save($event2);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->call(
            Request::METHOD_GET,
            '/api/events',
            [
                "dateOfEvent" => Carbon::now()->format(\DateTime::ATOM)
            ]
        );

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function testItCanReadOneEvent(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event = Events::factory()->make();
        $user1->events()->save($event);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->get(sprintf('/api/events/%s', $event->id));

        $response->assertOk();
        $response->assertJson($event->toArray());
    }

    public function testItCanCreateEvent(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->postJson('/api/events', [
            'title' => Factory::create()->title,
            'content' => Factory::create()->text,
            'valid_from' => Carbon::now()->addHour()->format(\DateTime::ATOM),
            'valid_to' => Carbon::now()->addDay()->format(\DateTime::ATOM),
            'gps_lat' => Factory::create()->latitude,
            'gps_lng' => Factory::create()->longitude,
        ]);

        $response->assertOk();
    }

    public function testItCanUpdateEvents(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event = Events::factory()->make();
        $user1->events()->save($event);

        Sanctum::actingAs(
            $user1,
            ['base-permission']
        );

        $response = $this->putJson(sprintf('/api/events/%s', $event->id), [
            'title' => 'I really need a random title',
            'content' => Factory::create()->text,
            'valid_from' => Carbon::now()->addHour()->format(\DateTime::ATOM),
            'valid_to' => Carbon::now()->addDay()->format(\DateTime::ATOM),
            'gps_lat' => Factory::create()->latitude,
            'gps_lng' => Factory::create()->longitude,
        ]);

        $response->assertOk();
    }

    public function testItCannotUpdateOtherUsersEvents(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event = Events::factory()->make();
        $user1->events()->save($event);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->putJson(sprintf('/api/events/%s', $event->id), [
            'title' => 'I really need a random title',
            'content' => Factory::create()->text,
            'valid_from' => Carbon::now()->addHour()->format(\DateTime::ATOM),
            'valid_to' => Carbon::now()->addDay()->format(\DateTime::ATOM),
            'gps_lat' => Factory::create()->latitude,
            'gps_lng' => Factory::create()->longitude,
        ]);

        $response->assertForbidden();
    }

    public function testItCanDeleteEvents(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event = Events::factory()->make();
        $user1->events()->save($event);

        Sanctum::actingAs(
            $user1,
            ['base-permission']
        );

        $response = $this->delete(sprintf('/api/events/%s', $event->id));

        $response->assertOk();
    }

    public function testItCannotDeleteOtherUsersEvents(): void
    {
        $user1 = User::factory()->create();

        assert($user1 instanceof User);

        $event = Events::factory()->make();
        $user1->events()->save($event);

        Sanctum::actingAs(
            User::factory()->create(),
            ['base-permission']
        );

        $response = $this->delete(sprintf('/api/events/%s', $event->id));

        $response->assertForbidden();
    }
}
