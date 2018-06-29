<?php

namespace Tests\API;

use KRLX\Track;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackTest extends TestCase
{
    use RefreshDatabase;

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
            'description' => 'A track only for epic shows. Shows will be rejected if they are not epic.'
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
        $track = factory(Track::class)->create();

        $request = $this->json('GET', "/api/v1/tracks/{$track->id}");

        $request->assertStatus(200)
                ->assertJson([
                    'id' => $track->id,
                    'name' => $track->name
                ]);
    }

    /**
     * Test that deleting a track via the API soft-deletes it.
     *
     * @return void
     */
    public function testAPITrackDeleteSoftDeletes()
    {
        $track = factory(Track::class)->create();

        $request = $this->json('DELETE', "/api/v1/tracks/{$track->id}");

        $request->assertStatus(204);
        $this->assertNull(Track::find($track->id));
        $this->assertNotNull(Track::withTrashed()->find($track->id));
    }
}
