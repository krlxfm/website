<?php

namespace Tests\Unit;

use KRLX\Show;
use KRLX\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $show;

    public function setUp()
    {
        parent::setUp();

        $this->show = factory(Show::class)->create();
        $this->user = factory(User::class)->states('contract_ok')->create();
    }

    /**
     * Test that changing the ID length doesn't break things.
     *
     * @return void
     */
    public function testDifferentIdLength()
    {
        $current_id_length = config('defaults.show_id_length');
        config(['defaults.show_id_length' => $current_id_length + 1]);

        $show = factory(Show::class)->create();
        $this->assertEquals($current_id_length + 1, strlen($show->id));

        config(['defaults.show_id_length' => $current_id_length]);
    }

    /**
     * Test that adding hosts without explicitly marking their invitation as
     * accepted, marks them as invitees rather than hosts.
     *
     * @return void
     */
    public function testDefaultUserAddIsInvitee()
    {
        $this->show->hosts()->attach($this->user);
        $this->assertContains($this->user->id, $this->show->invitees()->pluck('id'));
        $this->assertNotContains($this->user->id, $this->show->hosts()->pluck('id'));
    }

    /**
     * Test that inviting a user to a show by itself does not mark the show
     * as "boosted".
     *
     * @return void
     */
    public function testInvitationDoesNotMarkShowBoosted()
    {
        $this->show->hosts()->attach($this->user);
        $this->assertFalse($this->show->boosted);
    }

    /**
     * Test that inviting a user to a show by itself does not mark the show
     * as "boosted", even if we mark the invitation as bearing Priority Boost.
     *
     * @return void
     */
    public function testBoostedInvitationDoesNotMarkShowBoosted()
    {
        $this->show->hosts()->attach($this->user, ['boost' => 'S']);
        $this->assertFalse($this->show->boosted);
    }

    /**
     * Test that a single-host show, not marked as Priority Boost, is not
     * treated as boosted.
     *
     * @return void
     */
    public function testNonBoostedJoinDoesNotMarkShowBoosted()
    {
        $this->show->hosts()->attach($this->user, ['accepted' => true]);
        $this->assertCount(1, $this->show->hosts);
        $this->assertFalse($this->show->boosted);
    }

    /**
     * Test that a single-host show, marked as Priority Boost, *is* treated
     * as boosted.
     *
     * @return void
     */
    public function testBoostedJoinDoesMarkShowBoosted()
    {
        $this->show->hosts()->attach($this->user, ['accepted' => true, 'boost' => 'S']);
        $this->assertCount(1, $this->show->hosts);
        $this->assertTrue($this->show->boosted);
    }

    /**
     * Test that a show which doesn't appear on the calendar does not have a
     * next show.
     *
     * @return void
     */
    public function testUnpublishedShowsDontHaveNext()
    {
        $this->assertNull($this->show->day);
        $this->assertNull($this->show->next);
    }

    /**
     * Test that a show can find out when the following show is.
     *
     * @return void
     */
    public function testShowPrevAndNext()
    {
        $this->show->day = 'Saturday';
        $this->show->start = '12:00';
        $this->show->end = '13:00';

        $next_show = factory(Show::class)->create([
            'track_id' => $this->show->track->id,
            'term_id' => $this->show->term->id,
            'submitted' => true,
            'day' => 'Saturday',
            'start' => '13:00',
            'end' => '14:00',
        ]);

        $this->assertEquals($next_show->id, $this->show->next->id);
    }
}
