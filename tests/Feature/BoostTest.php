<?php

namespace Tests\Feature;

use KRLX\Show;
use KRLX\Term;
use KRLX\User;
use KRLX\Track;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoostTest extends TestCase
{
    use RefreshDatabase;

    public $boost;
    public $show;
    public $track;
    public $term;
    public $user;
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('db:seed');
        $this->track = factory(Track::class)->create([
            'active' => true,
        ]);
        $this->term = factory(Term::class)->create([
            'status' => 'active',
        ]);
        $this->user = factory(User::class)->states('contract_ok')->create();
        $this->show = factory(Show::class)->create([
            'id' => 'SHOW01',
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => false,
        ]);
        $this->show->hosts()->attach($this->user, ['accepted' => true]);
        $this->boost = $this->user->boosts()->create([
            'show_id' => $this->show->id,
            'type' => 'zone',
        ]);
        $this->session = $this->actingAs($this->user);
    }

    /**
     * Test that the user has access to the Priority Boost assignment screen.
     *
     * @return void
     */
    public function testUserWithBoostCanAccessBoostAssignments()
    {
        $request = $this->get('/shows/boost');
        $request->assertOk()
                ->assertSee(config('defaults.boost.zone'));
    }

    /**
     * Test that the user can access the redemption screen for any of their own
     * upgrade certificates.
     *
     * @return void
     */
    public function testUserCanRedeemOwnEligibleCertificate()
    {
        $request = $this->get("/shows/boost/{$this->boost->id}");
        $request->assertOk()
                ->assertSee(config('defaults.boost.zone'));
    }
}
