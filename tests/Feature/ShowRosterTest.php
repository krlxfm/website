<?php

namespace Tests\Feature;

use KRLX\Show;
use KRLX\Term;
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

        // Create user accounts
        $three_term_sophomore = factory(User::class)->states('carleton', 'contract_ok')->create([
            'year' => date('Y') + 2,
        ]);
        $zero_term_junior = factory(User::class)->states('carleton', 'contract_ok')->create([
            'year' => date('Y') + 1,
        ]);

        // Create an old term
        $old_term = factory(Term::class)->states('2015-TEST')->create();

        // Issue three experience points to the sophomore account
        $three_term_sophomore->points()->createMany([
            ['term_id' => $old_term->id, 'status' => 'issued'],
            ['term_id' => $old_term->id, 'status' => 'issued'],
            ['term_id' => $old_term->id, 'status' => 'issued'],
        ]);

        // Create the shows
        $show_G3 = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'updated_at' => $now,
            'submitted' => true,
        ]);
        $show_J2 = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'updated_at' => $now,
            'submitted' => true,
        ]);
        $show_J2_old = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'updated_at' => $now->subHour(),
            'submitted' => true,
        ]);

        // Link the hosts to the shows
        $show_G3->hosts()->attach($three_term_sophomore, ['accepted' => true]);
        $show_J2->hosts()->attach($zero_term_junior, ['accepted' => true]);
        $show_J2_old->hosts()->attach($zero_term_junior, ['accepted' => true]);

        // Make the request to the All Shows page using a board account
        $request = $this->session->get('/shows/all');

        // Verify that priorities are set correctly
        $this->assertEquals('G', $three_term_sophomore->priority->zone());
        $this->assertEquals('G', $three_term_sophomore->priorityAsOf($this->term->id)->zone());
        $this->assertEquals('G3', $show_G3->priority->code());

        $this->assertEquals('J', $zero_term_junior->priority->zone());
        $this->assertEquals('J2', $show_J2->priority->code());

        // This is backwards from what you might think it is: we're checking
        // that the "old" J2 show does indeed have an updated_at time which is
        // before the "new" J2 show.
        $this->assertLessThan($show_J2->updated_at, $show_J2_old->updated_at);

        // Check that shows appear in priority order.
        $request->assertOk()
                ->assertSeeInOrder([$show_G3->title, $show_J2_old->title, $show_J2->title]);
    }
}
