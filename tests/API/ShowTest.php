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
}
