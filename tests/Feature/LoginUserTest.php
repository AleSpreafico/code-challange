<?php


namespace Tests\Feature;


use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    public function testUserDetailsCanBeRetrieved()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['view-profile']
        );

        $response = $this->get('/api/user');

        $response->assertOk();
    }
}
