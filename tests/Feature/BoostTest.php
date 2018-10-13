<?php

namespace Tests\Feature;

use KRLX\Show;
use Tests\AuthenticatedTestCase;

class BoostTest extends AuthenticatedTestCase
{
    public $boosted_show;
    public $show;

    public function setUp()
    {
        parent::setUp();

        $this->show = factory(Show::class)->create(['term_id' => $this->term->id]);
        $this->boosted_show = factory(Show::class)->create(['term_id' => $this->term->id]);

        $this->show->hosts()->attach($this->carleton, ['accepted' => true]);
        $this->boosted_show->hosts()->attach($this->carleton, ['accepted' => true]);

        $this->show->hosts()->attach($this->board, ['accepted' => true]);
        $this->boosted_show->hosts()->attach($this->board, ['accepted' => true]);
        $this->board->boosts()->create(['show_id' => $this->boosted_show->id, 'term_id' => $this->term->id, 'type' => 'A1']);
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
}
