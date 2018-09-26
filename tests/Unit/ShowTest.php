<?php

namespace Tests\Unit;

use KRLX\Show;
use KRLX\User;
use KRLX\Track;
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
        $this->show->hosts()->attach($this->user);
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
        $this->show->hosts()->attach($this->user, ['accepted' => true]);
        $this->user->boosts()->create([
            'show_id' => $this->show->id,
            'type' => 'zone',
        ]);

        $this->assertCount(1, $this->show->hosts);
        $this->assertTrue($this->show->boosted);
    }

    /**
     * Test that a One-Zone Upgrade Certificate upgrades a show's priority
     * by the equivalent of one term.
     *
     * @return void
     */
    public function testOneZoneUpgrade()
    {
        $this->show->hosts()->attach($this->user, ['accepted' => true]);
        $this->user->boosts()->create([
            'show_id' => $this->show->id,
            'type' => 'zone',
        ]);

        $this->assertEquals(0, $this->user->priority->terms);
        $this->assertEquals('J', $this->user->priority->zone());
        $this->assertEquals(1, $this->show->priority->terms);
        $this->assertEquals('I', $this->show->priority->zone());
    }

    /**
     * Test that an A1 Upgrade Certificate successfully override's a show's
     * priority to A1.
     *
     * @return void
     */
    public function testA1Upgrade()
    {
        $this->show->hosts()->attach($this->user, ['accepted' => true]);
        $this->user->boosts()->create([
            'show_id' => $this->show->id,
            'type' => 'A1',
        ]);

        $this->assertEquals('A1', $this->show->priority->code());
        $this->assertEquals(1000, $this->show->priority->terms);
    }

    /**
     * Test that all A1 Boosted shows are created equal - that is, a show with
     * two A1 Boosts doesn't leapfrog over shows with just one.
     *
     * @return void
     */
    public function testOnlyOneA1UpgradePerShow()
    {
        $this->show->hosts()->attach($this->user, ['accepted' => true]);
        $this->user->boosts()->create([
            'show_id' => $this->show->id,
            'type' => 'A1',
        ]);

        $otherUser = factory(User::class)->states('contract_ok')->create();
        $this->show->hosts()->attach($otherUser, ['accepted' => true]);
        $otherUser->boosts()->create([
            'show_id' => $this->show->id,
            'type' => 'A1',
        ]);

        $this->assertEquals('A1', $this->show->priority->code());
        $this->assertEquals(1000, $this->show->priority->terms);
    }

    /**
     * Test that Board Priority Upgrade Certificates don't actually change
     * a show's priority code, but do flag the show as having Board Priority.
     *
     * @return void
     */
    public function testBoardUpgrade()
    {
        $this->show->hosts()->attach($this->user, ['accepted' => true]);
        $code = $this->show->priority->code();

        $this->user->boosts()->create([
            'show_id' => $this->show->id,
            'type' => 'S',
        ]);

        $this->assertEquals($code, $this->show->priority->code());
        $this->assertTrue($this->show->board_boost);
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
        $current_show = factory(Show::class)->create([
            'track_id' => $this->show->track->id,
            'term_id' => $this->show->term->id,
            'submitted' => true,
            'day' => 'Saturday',
            'start' => '12:00',
            'end' => '13:00',
        ]);

        $next_show = factory(Show::class)->create([
            'track_id' => $this->show->track->id,
            'term_id' => $this->show->term->id,
            'submitted' => true,
            'day' => 'Saturday',
            'start' => '13:00',
            'end' => '14:00',
        ]);

        $this->assertNotNull($current_show->start);
        $this->assertNotNull($current_show->end);
        $this->assertNotNull($current_show->day);

        $this->assertEquals($next_show->id, $current_show->next->id);
    }

    /**
     * Test "Next Show" for a track, such as Bandemonium.
     *
     * @return void
     */
    public function testBandemoniumAsNextShow()
    {
        $current_show = factory(Show::class)->create([
            'track_id' => $this->show->track->id,
            'term_id' => $this->show->term->id,
            'submitted' => true,
            'day' => 'Saturday',
            'start' => '12:00',
            'end' => '13:00',
        ]);

        $track = factory(Track::class)->create([
            'active' => true,
            'weekly' => false,
            'start_day' => 'Saturday',
            'start_time' => '13:00',
            'end_time' => '14:00',
            'name' => 'Demo Track'
        ]);

        $this->show->term->track_managers = [
            $track->id => [$this->user->id]
        ];
        $this->show->term->save();

        $this->assertEquals('Demo Track', $current_show->next->title);
        $this->assertContains($this->user->id, $current_show->next->hosts->pluck('id'));
        $this->assertTrue(starts_with($current_show->next->id, 'TRACK-'));
    }
}
