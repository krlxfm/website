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

        $this->std_track = factory(Track::class)->create();
        $this->custom_track = factory(Track::class)->states('custom_field')->create();
        $this->non_recurring_track = factory(Track::class)->states('non_weekly')->create();
    }

    /**
     * Test that board members can create tracks. Unauthenticated and non-Board
     * accounts should not be able to create tracks.
     *
     * @return void
     */
    public function testTrackCreation()
    {
        $trackData = [
            'name' => 'Epic Shows',
            'description' => 'A track only for epic shows. Shows will be rejected if they are not epic.',
        ];

        $unauthenticated_req = $this->json('POST', '/api/v1/tracks', $trackData);
        $unauthenticated_req->assertStatus(401);

        $request = $this->actingAs($this->board, 'api')->json('POST', '/api/v1/tracks', $trackData);
        $request->assertStatus(201);

        $non_board_request = $this->actingAs($this->carleton, 'api')->json('POST', '/api/v1/tracks', $trackData);
        $non_board_request->assertStatus(403);
    }

    /**
     * Test that, once a track is created, it can be fetched. (Also, anyone can
     * request details about a track.)
     *
     * @return void
     */
    public function testSingleTrackQuery()
    {
        $request = $this->json('GET', "/api/v1/tracks/{$this->std_track->id}");

        $request->assertStatus(200)
                ->assertJson([
                    'id' => $this->std_track->id,
                    'name' => $this->std_track->name,
                ])
                ->assertJsonMissing(['created_at', 'updated_at', 'deleted_at']);
    }
}
