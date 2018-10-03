<?php

namespace Tests\Permission;

use KRLX\Show;
use KRLX\User;
use Tests\AuthenticatedTestCase;

class ShowPermissionTest extends AuthenticatedTestCase
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
     * Test that guests can't create shows, but Carleton accounts that have
     * signed the membership contract can.
     *
     * @return void
     */
    public function testShowCreationPermission()
    {
        $guest_req = $this->actingAs($this->guest)->get('/shows/create');
        $new_carl_req = $this->actingAs($this->new_carl)->get('/shows/create');
        $carleton_req = $this->actingAs($this->carleton)->get('/shows/create');

        $guest_req->assertStatus(403);
        $new_carl_req->assertStatus(302);
        $carleton_req->assertStatus(200);
    }

    /**
     * Test that guests and Carleton users who are not hosts of a show can't
     * view the contents of a show. Board members can view all shows, and hosts
     * can view the shows they're a part of.
     *
     * @return void
     */
    public function testShowViewingPermission()
    {
        $routes = ["", "hosts", "content", "schedule"];
        foreach($routes as $route) {
            $guest_req = $this->actingAs($this->guest)->get("/shows/{$this->show->id}/$route");
            $carleton_req = $this->actingAs($this->carleton)->get("/shows/{$this->show->id}/$route");
            $host_req = $this->actingAs($this->host)->get("/shows/{$this->show->id}/$route");
            $board_req = $this->actingAs($this->board)->get("/shows/{$this->show->id}/$route");

            $guest_req->assertStatus(403);
            $carleton_req->assertStatus(302)
                         ->assertRedirect("/shows/join/{$this->show->id}");
            $host_req->assertStatus(200);
            $board_req->assertStatus(200);
        }
    }

    /**
     * Test that only hosts and board members can delete a show.
     *
     * @return void
     */
    public function testShowDeletePermission()
    {
        $guest_req = $this->actingAs($this->guest)->get("/shows/{$this->show->id}/delete");
        $carleton_req = $this->actingAs($this->carleton)->get("/shows/{$this->show->id}/delete");
        $host_req = $this->actingAs($this->host)->get("/shows/{$this->show->id}/delete");
        $board_req = $this->actingAs($this->board)->get("/shows/{$this->show->id}/delete");

        $guest_req->assertStatus(403);
        $carleton_req->assertStatus(403);
        $host_req->assertStatus(200);
        $board_req->assertStatus(200);
    }
}
