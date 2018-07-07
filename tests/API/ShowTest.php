<?php

namespace Tests\Feature;

use KRLX\Term;
use KRLX\Track;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends TestCase
{
    /**
     * Assert that authentication is required to make calls on the API.
     *
     * @return void
     */
    public function testUnauthenticatedCallsNotPermitted()
    {
        $track = factory(Track::class)->create(['active' => true]);
        $term = factory(Term::class)->create(['accepting_applications' => true]);

        $request = $this->json('POST', '/api/v1/shows', [
            'title' => 'Gray Duck',
            'track_id' => $track->id,
            'term_id' => $term->id
        ]);

        $request->assertStatus(401);
    }

    /**
     * Assert that shows can be created via the API.
     *
     * @return void
     */
    public function testShowsCanBeCreated()
    {
        $track = factory(Track::class)->create(['active' => true]);
        $term = factory(Term::class)->create(['accepting_applications' => true]);

        $request = $this->json('POST', '/api/v1/shows', [
            'title' => 'Gray Duck',
            'track_id' => $track->id,
            'term_id' => $term->id
        ]);

        $request->assertStatus(201);
    }

    /**
     * Test that shows can be created with an empty title and still create OK
     * (they'll have a title generated for them).
     *
     * @return void
     */
    public function testShowsCanGenerateTitles()
    {
        $track = factory(Track::class)->create(['active' => true]);
        $term = factory(Term::class)->create(['accepting_applications' => true]);

        $request = $this->json('POST', '/api/v1/shows', [
            'track_id' => $track->id,
            'term_id' => $term->id
        ]);

        $request->assertStatus(201);
    }
}
