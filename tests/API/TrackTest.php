<?php

namespace Tests\API;

use KRLX\Track;
use Tests\AuthenticatedTestCase;

class ShowTest extends AuthenticatedTestCase
{
    public $standard_track;
    public $custom_track;
    public $non_recurring_track;

    public function setUp()
    {
        parent::setUp();

        $this->standard_track = factory(Track::class)->create();
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
        $request = $this->json('GET', "/api/v1/tracks/{$this->standard_track->id}");

        $request->assertStatus(200)
                ->assertJson([
                    'id' => $this->standard_track->id,
                    'name' => $this->standard_track->name,
                ])
                ->assertJsonMissing(['created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * Test that deleting a track via the API soft-deletes it.
     *
     * @return void
     */
    public function testAPITrackDeleteSoftDeletes()
    {
        $request = $this->json('DELETE', "/api/v1/tracks/{$this->standard_track->id}");

        $request->assertStatus(204);
        $this->assertNull(Track::find($this->standard_track->id));
        $this->assertNotNull(Track::withTrashed()->find($this->standard_track->id));
    }

    /**
     * Test that the "index" route returns all tracks in the system (not
     * including deleted ones).
     *
     * @return void
     */
    public function testTrackIndexReturnsTracks()
    {
        $secondTrack = factory(Track::class)->create();
        $deletedTrack = factory(Track::class)->create();
        $deletedTrack->delete();

        $request = $this->json('GET', '/api/v1/tracks');

        $request->assertOk()
                ->assertJsonFragment(['id' => $this->standard_track->id])
                ->assertJsonFragment(['id' => $secondTrack->id])
                ->assertJsonMissing(['id' => $deletedTrack->id]);
    }

    /**
     * Test that PATCH requests ONLY update the requested data.
     *
     * @return void
     */
    public function testPatchOnlyUpdatesRequestedData()
    {
        $name = $this->standard_track->name;

        $request = $this->actingAs($this->board, 'api')->json('PATCH', "/api/v1/tracks/{$this->standard_track->id}", [
            'description' => 'A patched description.',
        ]);

        $request->assertOk()
                ->assertJson([
                    'description' => 'A patched description.',
                    'name' => $name,
                ]);
    }

    /**
     * Test that PUT requests FAIL if data is missing.
     *
     * @return void
     */
    public function testPutFailsWithMissingAttribute()
    {
        $request = $this->actingAs($this->board, 'api')->json('PUT', "/api/v1/tracks/{$this->standard_track->id}", [
            'description' => 'A patched description.',
        ]);

        $request->assertStatus(422);
        $this->assertNotEquals('A patched description.', Track::find($this->standard_track->id)->description);
    }
}
