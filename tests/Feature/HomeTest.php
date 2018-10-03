<?php

namespace Tests\Feature;

use KRLX\Term;
use Tests\AuthenticatedTestCase;

class HomeTest extends AuthenticatedTestCase
{
    /**
     * Verify that the user can see their home screen.
     *
     * @return void
     */
    public function testHomeScreen()
    {
        $request = $this->actingAs($this->carleton)->get('/home');
        $request->assertOk()
                ->assertSee($this->carleton->priority->html());
    }

    /**
     * Verify that the Priority Upgrades module does NOT appear if the user
     * does NOT have a certificate available to redeem.
     *
     * @return void
     */
    public function testPriorityUpgradesDontAppearIfUserDoesntHaveAny()
    {
        $alt_term = factory(Term::class)->create();

        $this->carleton->boosts()->create(['term_id' => $alt_term->id]);
        $this->assertCount(0, $this->carleton->boosts->whereIn('term_id', [null, $this->term->id]));

        $board_req = $this->actingAs($this->carleton)->get('/home');
        $board_req->assertDontSee('Priority Upgrade Certificates');
    }

    /**
     * Verify that the Priority Upgrades module appears if the user has
     * certificates available to redeem.
     *
     * @return void
     */
    public function testPriorityUpgradeAppearIfEligible()
    {
        $this->carleton->boosts()->create();
        $this->assertCount(1, $this->carleton->boosts->whereIn('term_id', [null, $this->term->id]));

        $board_req = $this->actingAs($this->carleton)->get('/home');
        $board_req->assertSee('Priority Upgrade Certificates');
    }
}
