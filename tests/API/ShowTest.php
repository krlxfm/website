<?php

namespace Tests\API;

use KRLX\Show;
use KRLX\Term;
use KRLX\Track;
use Tests\API\APITestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends APITestCase
{
    use RefreshDatabase;

    public $show;
    public $term;
    public $track;

    public function setUp()
    {
        parent::setUp();

        $this->term = factory(Term::class)->create(['accepting_applications' => true]);
        $this->track = factory(Track::class)->create(['active' => true]);
        $this->show = factory(Show::class)->create([
            'term_id' => $this->term->id,
            'track_id' => $this->track->id
        ]);
    }

    /**
     * Assert that shows can be created via the API... when signed in.
     *
     * @return void
     */
    public function testShowsCanBeCreated()
    {
        $this->assertAuthenticatedAs($this->user, 'api');

        $request = $this->json('POST', '/api/v1/shows', [
            'title' => 'Gray Duck',
            'track_id' => $this->track->id,
            'term_id' => $this->term->id
        ]);

        $request->assertStatus(201);
        $show = Show::find($request->getData()->id);
        $this->assertContains($show->id, $this->user->shows()->pluck('id'));
        $this->assertContains($this->user->id, $show->hosts()->pluck('id'));
        $this->assertNotContains($this->user->id, $show->invitees()->pluck('id'));
    }

    /**
     * Test that shows can be created with an empty title and still create OK
     * (they'll have a title generated for them).
     *
     * @return void
     */
    public function testShowsCanGenerateTitles()
    {
        $request = $this->json('POST', '/api/v1/shows', [
            'track_id' => $this->track->id,
            'term_id' => $this->term->id
        ]);

        $request->assertStatus(201);
    }
}
