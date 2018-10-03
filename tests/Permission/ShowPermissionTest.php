<?php

namespace Tests\Permission;

use KRLX\Show;
use KRLX\User;
use Tests\AuthenticatedTestCase;

class ShowPermissionTest extends AuthenticatedTestCase
{
    public $show;
    public $new_carl;

    public function setUp()
    {
        parent::setUp();
        $this->new_carl = factory(User::class)->states('carleton_new')->create();
        $this->show = factory(Show::class)->create(['term_id' => $this->term->id]);
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
}
