<?php

namespace Tests\Permission;

use KRLX\Show;
use KRLX\User;
use Tests\AuthenticatedTestCase;

class ShowAdminPermissionTest extends AuthenticatedTestCase
{
    public $host;
    public $show;

    public function setUp()
    {
        parent::setUp();

        $this->host = factory(User::class)->states('carleton', 'contract_ok')->create();
        $this->show = factory(Show::class)->create(['term_id' => $this->term->id]);
        $this->show->hosts()->attach($this->host, ['accepted' => true]);
    }

    /**
     * Test that board members have access to ALL restricted routes, while
     * other accounts don't have ANY access to these routes.
     *
     * @return void
     */
    public function testBoardAccessToRestrictedRoutes()
    {
        $routes = ["/shows/all", "/shows/download", "/shows/djs", "/schedule/build"];

        foreach($routes as $route) {
            $guest_req = $this->actingAs($this->guest)->get("$route");
            $carleton_req = $this->actingAs($this->carleton)->get("$route");
            $host_req = $this->actingAs($this->host)->get("$route");
            $board_req = $this->actingAs($this->board)->get("$route");

            $guest_req->assertStatus(403);
            $carleton_req->assertStatus(403);
            $host_req->assertStatus(403);
            $board_req->assertStatus(200);
        }
    }
}
