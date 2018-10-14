<?php

namespace Tests\API;

use Tests\AuthenticatedTestCase;

class UserTest extends AuthenticatedTestCase
{
    /**
     * Test the user searching.
     *
     * @return void
     */
    public function testUserSearching()
    {
        $request = $this->actingAs($this->carleton, 'api')->json('GET', '/api/v1/users', ['query' => $this->new_carl->email]);
        $request->assertOk()
                ->assertJsonFragment(['id' => $this->new_carl->id]);
    }
}
