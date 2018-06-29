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
}
