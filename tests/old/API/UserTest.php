<?php

namespace Tests\API;

use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends APITestCase
{
    use RefreshDatabase;

    /**
     * Test the user searching.
     *
     * @return void
     */
    public function testUserSearching()
    {
        $request = $this->json('GET', '/api/v1/users', ['query' => $this->user->email]);
        $request->assertOk()
                ->assertJsonFragment(['id' => $this->user->id]);
    }
}
