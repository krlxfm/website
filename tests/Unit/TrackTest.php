<?php

namespace Tests\Unit;

use KRLX\Show;
use KRLX\Track;
use Tests\UnitBaseCase;

class TrackTest extends UnitBaseCase
{
    /**
     * Verify that the static dummy shows generator does create precisely one
     * show for each non-recurring show track.
     *
     * @return void
     */
    public function testDummyShowsCreatesCorrectShows()
    {
        $track_one = factory(Track::class)->create([
            'weekly' => false,
            'start_day' => 'Sunday',
            'start_time' => '17:00',
            'end_time' => '19:00',
            'name' => 'Track 1',
        ]);

        $track_two = factory(Track::class)->create([
            'weekly' => false,
            'start_day' => 'Monday',
            'start_time' => '17:00',
            'end_time' => '19:00',
            'name' => 'Track 2',
        ]);

        $track_null = factory(Track::class)->create([
            'weekly' => false,
            'name' => 'Track Null',
        ]);

        $shows = collect(Track::dummyShows());
        $this->assertCount(2, $shows);
        $this->assertNotContains('Track Null', $shows->pluck('title'));
    }
}
