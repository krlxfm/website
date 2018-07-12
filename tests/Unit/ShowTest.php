<?php

namespace Tests\Unit;

use KRLX\User;
use KRLX\Show;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $this->user = factory(User::class)->create();
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
}