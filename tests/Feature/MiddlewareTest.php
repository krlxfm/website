<?php

namespace Tests\Feature;

use KRLX\Show;
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
    public function testMembershipContractMiddlewareWithGoodUser()
    {
        $goodUser = factory(User::class)->states('contract_ok')->create();

        $ok_request = $this->actingAs($goodUser)->get("/shows/my/{$this->term->id}");

        $this->assertContains($this->term->id, $goodUser->points->pluck('term_id'));
        $ok_request->assertOk()->assertViewIs('shows.my');
    }

    /**
     * Test the MembershipContract middleware on various routes with a bad user.
     * This user should be redirected to sign the contract before proceeding.
     *
     * @return void
     */
    public function testMembershipContractMiddlewareWithBadUser()
    {
        $show = factory(Show::class)->create([
            'term_id' => $this->term->id
        ]);
        $routes = ["shows/my/{$this->term->id}", "shows", "shows/{$show->id}"];
        foreach($routes as $route) {
            $bad_request = $this->get($route);
            $this->assertEquals(302, $bad_request->status(), "The request to $route got through.");
            $bad_request->assertRedirect(route('legal.contract'))
                        ->assertSessionHas('url.intended', $route);
        }
    }

    /**
     * Test that, after redirection and signing the contract, the user can
     * proceed to their intended destination.
     *
     * @return void
     */
    public function testSigningContract()
    {
        $show = factory(Show::class)->create([
            'term_id' => $this->term->id
        ]);
        $show->hosts()->attach($this->user->id, ['accepted' => true]);
        $first_request = $this->get("/shows/{$show->id}");
        $second_request = $this->post("/contract", ['term' => $this->term->id, 'contract' => true]);
        $second_request->assertRedirect("/shows/{$show->id}");
        $this->assertContains($this->term->id, $this->user->points()->where('status', 'provisioned')->get()->pluck('term_id'));
    }
}
