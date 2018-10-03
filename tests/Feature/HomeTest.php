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
     * Verify that the Priority Upgrades module ONLY appears for users who do
     * have an eligible Priority Upgrade Certificate that they can use.
     *
     * @return void
     */
    public function testPriorityUpgradesModuleOnlyAppearsIfNeeded()
    {
        $alt_term = factory(Term::class)->create([
            'on_air' => $this->term->on_air->copy()->subWeek(),
            'off_air' => $this->term->off_air->copy()->subWeek(),
        ]);

        $this->carleton->boosts()->create(['term_id' => $alt_term->id]);
        $this->board->boosts()->create();

        $carleton_req = $this->actingAs($this->carleton)->get('/home');
        $board_req = $this->actingAs($this->board)->get('/home');

        $this->assertCount(0, $this->carleton->boosts->whereIn('term_id', [null, $this->term->id]));
        $this->assertCount(1, $this->carleton->boosts);
        $carleton_req->assertDontSee('Priority Upgrade Certificates');

        $this->assertCount(1, $this->board->boosts->whereIn('term_id', [null, $this->term->id]));
        $board_req->assertSee('Priority Upgrade Certificates');
    }
}
