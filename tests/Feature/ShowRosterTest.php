<?php

namespace Tests\Feature;

use KRLX\Show;
use KRLX\User;
use KRLX\Track;
use Carbon\Carbon;
use Tests\AuthenticatedTestCase;

class ShowRosterTest extends AuthenticatedTestCase
{
    public $track;
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->track = factory(Track::class)->create();
        $this->session = $this->actingAs($this->board);
    }

    /**
     * Test that shows appear in "priority order". That is, shows with more
     * experienced hosts should appear first, then those with older hosts, and
     * then the final tie-break should be modification time.
     *
     * @return void
     */
    public function testAllShowsAppearInPriorityOrder()
    {
        $now = Carbon::now();
        $three_term_sophomore = factory(User::class)->states('carleton', 'contract_ok')->create([
            'year' => date('Y') + 2,
        ]);
        $zero_term_junior = factory(User::class)->states('carleton', 'contract_ok')->create([
            'year' => date('Y') + 1,
        ]);
        $three_term_sophomore->points()->createMany([
            ['term_id' => $this->term->id, 'status' => 'issued'],
            ['term_id' => $this->term->id, 'status' => 'issued'],
            ['term_id' => $this->term->id, 'status' => 'issued'],
        ]);
        $show_G3 = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'updated_at' => $now,
            'submitted' => true,
        ]);
        $show_G3->hosts()->attach($three_term_sophomore, ['accepted' => true]);

        $show_J2 = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'updated_at' => $now,
            'submitted' => true,
        ]);
        $show_J2->hosts()->attach($zero_term_junior, ['accepted' => true]);

        $show_J2_old = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'updated_at' => $now->subHour(),
            'submitted' => true,
        ]);
        $show_J2_old->hosts()->attach($zero_term_junior, ['accepted' => true]);

        $request = $this->session->get('/shows/all');

        $this->assertEquals('G', $three_term_sophomore->priority->zone());
        $this->assertEquals('G3', $show_G3->priority->code());

        $this->assertEquals('J', $zero_term_junior->priority->zone());
        $this->assertEquals('J2', $show_J2->priority->code());

        $this->assertLessThan($show_J2->updated_at, $show_J2_old->updated_at);

        $request->assertOk()
                ->assertSeeInOrder([$show_G3->title, $show_J2_old->title, $show_J2->title]);
    }
}
