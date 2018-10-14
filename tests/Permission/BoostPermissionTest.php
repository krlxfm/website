<?php

namespace Tests\Permission;

use Tests\AuthenticatedTestCase;

class BoostPermissionTest extends AuthenticatedTestCase
{
    /**
     * Test that all users (who can access show applications) can also access
     * the Priority Boost system, even if they don't have any active upgrades.
     *
     * @return void
     */
    public function testBoostIndexAccess()
    {
        $guest_req = $this->actingAs($this->guest)->get('/shows/boost');
        $new_carl_req = $this->actingAs($this->new_carl)->get('/shows/boost');
        $carleton_req = $this->actingAs($this->carleton)->get('/shows/boost');
        $board_req = $this->actingAs($this->board)->get('/shows/boost');

        $guest_req->assertStatus(403);
        $new_carl_req->assertStatus(302);
        $carleton_req->assertStatus(200);
        $board_req->assertStatus(200);
    }

    /**
     * Test that users can only redeem their own boosts (including board users).
     *
     * @return void
     */
    public function testUsersCanOnlyRedeemOwnBoosts()
    {
        $boost = $this->carleton->boosts()->create(['term_id' => $this->term->id, 'type' => 'zone']);

        $carleton_req = $this->actingAs($this->carleton)->get("/shows/boost/{$boost->id}");
        $board_req = $this->actingAs($this->board)->get("/shows/boost/{$boost->id}");

        $carleton_req->assertStatus(200);
        $board_req->assertStatus(403);
    }
}
