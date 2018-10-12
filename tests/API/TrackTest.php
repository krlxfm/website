<?php

namespace Tests\API;

use KRLX\Track;
use Tests\AuthenticatedTestCase;

class ShowTest extends AuthenticatedTestCase
{
    public $std_track;
    public $custom_track;
    public $non_recurring_track;

    public function setUp()
    {
        parent::setUp();

        $std_track = factory(Track::class)->create();
        $custom_track = factory(Track::class)->states('custom_field')->create();
        $non_recurring_track = factory(Track::class)->states('non_weekly')->create();
    }

    /**
     * Test that board members can create tracks.
     *
     * @return void
     */
    public function testTrackCreation()
    {
        $trackData = [
            'name' => 'Epic Shows',
            'description' => 'A track only for epic shows. Shows will be rejected if they are not epic.',
        ];

        $request = $this->actingAs($this->board, 'api')->json('POST', '/api/v1/tracks', $trackData);
        $request->assertStatus(201);

        $non_board_request = $this->actingAs($this->carleton, 'api')->json('POST', '/api/v1/tracks', $trackData);
        $non_board_request->assertStatus(403);
    }
}
