<?php

namespace Tests\Permission;

use KRLX\Show;
use KRLX\User;
use Tests\AuthenticatedTestCase;

class BoardAppPermissionTest extends AuthenticatedTestCase
{
    public $host;

    public function setUp()
    {
        parent::setUp();

        $show = factory(Show::class)->create(['term_id' => $this->term->id]);
        $this->host = factory(User::class)->states('carleton', 'contract_ok')->create();
        $show->hosts()->attach($this->host, ['accepted' => true]);
        $this->host->givePermissionTo('apply for board seats');
    }

    /**
     * Test that all accounts have access to the landing page, but what they see
     * depends on whether or not their permission to apply has been set.
     *
     * @return void
     */
    public function testContentsOfLandingPage()
    {
        $guest_req = $this->actingAs($this->guest)->get('/board/apply');
        $carleton_req = $this->actingAs($this->carleton)->get('/board/apply');
        $host_req = $this->actingAs($this->host)->get('/board/apply');
        $board_req = $this->actingAs($this->board)->get('/board/apply');

        $guest_req->assertOk()
                  ->assertSee('Application Unavailable');
        $carleton_req->assertOk()
                     ->assertSee('Application Unavailable');
        $host_req->assertOk()
                 ->assertDontSee('Application Unavailable');
        $board_req->assertOk()
                  ->assertDontSee('Application Unavailable');
    }

    /**
     * Test that users must have permission to apply for board seats in order
     * to create a board application. Successful creation results in the user
     * being redirected to the application itself.
     *
     * @return void
     */
    public function testPermissionRequiredToCreateApplications()
    {
        $guest_req = $this->actingAs($this->guest)->get('/board/apply/start');
        $carleton_req = $this->actingAs($this->carleton)->get('/board/apply/start');
        $host_req = $this->actingAs($this->host)->get('/board/apply/start');
        $board_req = $this->actingAs($this->board)->get('/board/apply/start');

        $guest_req->assertStatus(403);
        $carleton_req->assertStatus(403);
        $host_req->assertStatus(302);
        $board_req->assertStatus(302);
    }
}
