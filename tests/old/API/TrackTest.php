<?php

namespace Tests\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use KRLX\Track;

class TrackTest extends APITestCase
{
    use RefreshDatabase;

    protected $track;

    public function setUp()
    {
        parent::setUp();
        $this->track = factory(Track::class)->create();
    }

    /**
     * Test that tracks can be created.
     * Given a track name and description, assert that the track is created,
     * and that the API call returns a status code 201.
     *
     * @return void
     */
    public function testTrackCreation()
    {
        $trackData = [
            'name' => 'Epic Shows',
            'description' => 'A track only for epic shows. Shows will be rejected if they are not epic.',
        ];

        $request = $this->json('POST', '/api/v1/tracks', $trackData);

        $request->assertStatus(201);
    }

    /**
     * Test that, once a track is created, it can be fetched.
     *
     * @return void
     */
    public function testSingleTrackQuery()
    {
        $request = $this->json('GET', "/api/v1/tracks/{$this->track->id}");

        $request->assertStatus(200)
                ->assertJson([
                    'id' => $this->track->id,
                    'name' => $this->track->name,
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
        $request = $this->json('DELETE', "/api/v1/tracks/{$this->track->id}");

        $request->assertStatus(204);
        $this->assertNull(Track::find($this->track->id));
        $this->assertNotNull(Track::withTrashed()->find($this->track->id));
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
                ->assertJsonFragment(['id' => $this->track->id])
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
        $name = $this->track->name;

        $request = $this->json('PATCH', "/api/v1/tracks/{$this->track->id}", [
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
        $request = $this->json('PUT', "/api/v1/tracks/{$this->track->id}", [
            'description' => 'A patched description.',
        ]);

        $request->assertStatus(422);
        $this->assertNotEquals('A patched description.', Track::find($this->track->id)->description);
    }
}
