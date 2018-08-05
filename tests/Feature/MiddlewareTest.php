<?php

namespace Tests\Feature;

use KRLX\Term;
use KRLX\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public $term;
    public $user;
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->term = factory(Term::class)->create();
        $this->user = factory(User::class)->create();
        $this->session = $this->actingAs($this->user);
    }

    /**
     * Test the "MembershipContract" middleware.
     * If we attempt to access a show application but have not signed the
     * memberhsip agreement for that term, we need to be redirected to that page
     * before we can continue. Users who have signed the contract can continue.
     *
     * @return void
     */
    public function testMembershipContractMiddleware()
    {
        $goodUser = factory(User::class)->states('contract_ok')->create();

        $ok_request = $this->actingAs($goodUser)->get("/shows/my/{$this->term->id}");
        $bad_request = $this->actingAs($this->user)->get("/shows/my/{$this->term->id}");

        $ok_request->assertOk()->assertViewIs('shows.my');
        $bad_request->assertRedirect(route('legal.contract'));
    }
}
