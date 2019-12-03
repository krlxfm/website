<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use KRLX\Term;
use KRLX\User;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $term;
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('db:seed');
        $this->user = factory(User::class)->states('carleton')->create();
        $this->term = factory(Term::class)->create();
        $this->session = $this->actingAs($this->user);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHomeScreen()
    {
        $request = $this->get('/home');
        $request->assertOk()
                ->assertSee($this->user->priority->html());
    }
}
