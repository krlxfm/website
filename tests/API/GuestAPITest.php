<?php

namespace Tests\API;

use KRLX\Term;
use KRLX\Track;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestAPITest extends TestCase
{
    use RefreshDatabase;

    /**
     * Assert that authentication is required to make calls on the API.
     *
     * @return void
     */
    public function testUnauthenticatedCallsNotPermitted()
    {
        $this->assertGuest('api');
        $term = factory(Term::class)->create(['accepting_applications' => true]);
        $track = factory(Track::class)->create(['active' => true]);

        $request = $this->json('POST', '/api/v1/shows', [
            'title' => 'Gray Duck',
            'track_id' => $track->id,
            'term_id' => $term->id,
        ]);

        $request->assertStatus(401);
    }
}
