<?php

namespace Tests\Feature;

use KRLX\Show;
use KRLX\Term;
use KRLX\User;
use KRLX\Track;
use Tests\AuthenticatedTestCase;

class BoostTest extends AuthenticatedTestCase
{
    public $boosted_show;
    public $show;
    public $board_a1;

    public function setUp()
    {
        parent::setUp();

        $this->show = factory(Show::class)->create(['title' => 'Unboosted Show', 'term_id' => $this->term->id]);
        $this->boosted_show = factory(Show::class)->create(['title' => 'A1 Boosted Show', 'term_id' => $this->term->id]);

        $this->show->hosts()->attach($this->carleton, ['accepted' => true]);
        $this->boosted_show->hosts()->attach($this->carleton, ['accepted' => true]);

        $this->show->hosts()->attach($this->board, ['accepted' => true]);
        $this->boosted_show->hosts()->attach($this->board, ['accepted' => true]);
        $this->carleton_a1 = $this->carleton->boosts()->create(['show_id' => $this->boosted_show->id, 'term_id' => $this->term->id, 'type' => 'A1']);
    }

    /**
     * Test that Board members automatically create a Board Upgrade Certificate
     * when creating a show, provided that they don't already have one for the
     * current term.
     *
     * @return void
     */
    public function testBoardMembersCreateBoardCertificatesOnCreation()
    {
        $first_request = $this->actingAs($this->board)->post('/shows', [
            'term_id' => $this->term->id,
            'track_id' => $this->show->track_id,
            'title' => 'Board Upgraded Show',
        ]);
        $second_request = $this->actingAs($this->board)->post('/shows', [
            'term_id' => $this->term->id,
            'track_id' => $this->show->track_id,
            'title' => 'Non-Upgraded Show',
        ]);

        $upgraded = Show::where('title', 'Board Upgraded Show')->first();
        $standard = Show::where('title', 'Non-Upgraded Show')->first();

        $this->assertTrue($upgraded->board_boost);
        $this->assertFalse($standard->board_boost);

        $this->assertEquals(1, $this->board->boosts()->where([['type', 'S'], ['term_id', $this->term->id]])->count());
    }

    /**
     * Test that Board members automatically create a Board Upgrade Certificate
     * when joining a show, provided that they don't already have one for the
     * current term.
     *
     * @return void
     */
    public function testBoardMembersCreateBoardCertificatesOnJoin()
    {
        $second_show = factory(Show::class)->create(['term_id' => $this->term->id]);

        $token_a = encrypt(['user' => $this->board->email, 'show' => $this->show->id]);
        $token_b = encrypt(['user' => $this->board->email, 'show' => $second_show->id]);

        $join_first = $this->actingAs($this->board)->put("/shows/join/{$this->show->id}", ['token' => $token_a]);
        $join_second = $this->actingAs($this->board)->put("/shows/join/{$second_show->id}", ['token' => $token_b]);

        $this->assertContains($this->board->id, $this->show->hosts->pluck('id'));
        $this->assertContains($this->board->id, $second_show->hosts->pluck('id'));
        $this->assertTrue($this->show->board_boost, 'The Board Upgrade Certificate was not correctly applied.');
        $this->assertFalse($second_show->board_boost, 'A second Board Upgrade Certificate was issued.');
        $this->assertEquals(1, $this->board->boosts()->where([['type', 'S'], ['term_id', $this->term->id]])->count());
    }

    /**
     * Test that the view successfully renders, even for users without a boost.
     *
     * @return void
     */
    public function testBoostIndexRenders()
    {
        $board_req = $this->actingAs($this->board)->get('/shows/boost');
        $carleton_req = $this->actingAs($this->carleton)->get('/shows/boost');

        $this->assertCount(0, $this->board->eligibleBoosts());
        $this->assertCount(1, $this->carleton->eligibleBoosts());

        $carleton_req->assertSee('A1 Upgrade Certificate');
    }

    /**
     * Test that a show with multiple One-Zone Upgrades appears okay in the
     * redemption screen.
     *
     * @return void
     */
    public function testZoneUpgradesCombineOnRedeemScreen()
    {
        $this->carleton->boosts()->create(['type' => 'zone', 'show_id' => $this->show->id]);
        $boost = $this->carleton->boosts()->create(['type' => 'zone']);

        $request = $this->actingAs($this->carleton)->get("/shows/boost/{$boost->id}");

        $request->assertSee($this->show->title);
    }

    /**
     * When a user has an upgrade certificate available and eligible shows,
     * clicking "Redeem" should show all available shows. In particular, shows
     * can only have one A1 or S certificate on them. As such, we should test
     * that only the eligible shows appear when redeeming a certificate.
     *
     * @return void
     */
    public function testRedeemingCertificateShowsEligibleShows()
    {
        $board_a1 = $this->board->boosts()->create(['term_id' => $this->term->id, 'type' => 'A1']);
        $board_s = $this->board->boosts()->create(['term_id' => $this->term->id, 'type' => 'S']);

        $carleton_a1_req = $this->actingAs($this->carleton)->get("/shows/boost/{$this->carleton_a1->id}");
        $board_a1_req = $this->actingAs($this->board)->get("/shows/boost/{$board_a1->id}");
        $board_s_req = $this->actingAs($this->board)->get("/shows/boost/{$board_s->id}");

        $carleton_a1_req->assertSee('Unboosted Show')
                        ->assertSee('A1 Boosted Show');
        $board_a1_req->assertSee('Unboosted Show')
                     ->assertDontSee('A1 Boosted Show');
        $board_s_req->assertSee('Unboosted Show')
                    ->assertSee('A1 Boosted Show');
    }

    /**
     * Test that redeeming in an invalid situation throws a 422 response. This
     * should happen under the following circumstances:.
     *
     * - Host/show mismatch
     * - Limited-use certificate duplication (applying a second A1 or S)
     * - Ineligible track
     * - Term-restricted certificate being used on a show of a different term
     *
     * @return void
     */
    public function testInvalidRedemptionThrowsError()
    {
        $past_term = factory(Term::class)->create([
            'status' => 'scheduled',
            'on_air' => $this->term->on_air->subWeek(),
            'off_air' => $this->term->off_air->subWeek(),
        ]);
        $rando = factory(User::class)->states('carleton', 'contract_ok')->create();
        $ineligible_track = factory(Track::class)->create(['boostable' => false]);
        $ineligible_track_show = factory(Show::class)->create([
            'track_id' => $ineligible_track->id,
            'term_id' => $this->term->id,
            'submitted' => true,
        ]);
        $ineligible_track_show->hosts()->attach($this->carleton, ['accepted' => true]);
        $past_show = factory(Show::class)->create([
            'term_id' => $past_term->id,
            'submitted' => true,
        ]);

        $rando_boost = $rando->boosts()->create(['type' => 'zone']);
        $term_lim_boost = $this->carleton->boosts()->create(['type' => 'zone', 'term_id' => $past_term->id]);
        $count_lim_boost = $this->carleton->boosts()->create(['type' => 'A1']);
        $carl_zone_boost = $this->carleton->boosts()->create(['type' => 'zone']);

        $host_mismatch_request = $this->actingAs($rando)->post("/shows/boost/{$rando_boost->id}", ['show_id' => $this->show->id]);
        $limited_request = $this->actingAs($this->carleton)->post("/shows/boost/{$count_lim_boost->id}", ['show_id' => $this->boosted_show->id]);
        $bad_track_request = $this->actingAs($this->carleton)->post("/shows/boost/{$carl_zone_boost->id}", ['show_id' => $ineligible_track_show->id]);
        $past_show_request = $this->actingAs($this->carleton)->post("/shows/boost/{$term_lim_boost->id}", ['show_id' => $this->show->id]);

        $this->assertEquals(302, $host_mismatch_request->status());
        $this->assertEquals(302, $limited_request->status());
        $this->assertEquals(302, $bad_track_request->status());
        $this->assertEquals(302, $past_show_request->status());
    }
}
