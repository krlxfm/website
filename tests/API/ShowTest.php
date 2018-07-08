<?php

namespace Tests\API;

use KRLX\Show;
use KRLX\Term;
use KRLX\User;
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

    /**
     * Test that only the current user's shows will be returned from the GET
     * /api/v1/shows route. This technically breaks REST conventions, but it's
     * worth it for simplicity's sake.
     *
     * @return void
     */
    public function testOnlyMyShowsReturnedFromIndex()
    {
        $show = factory(Show::class)->create([
            'term_id' => $this->term->id,
            'track_id' => $this->track->id
        ]);
        $show->hosts()->attach($this->user->id, ['accepted' => true]);

        $request = $this->json('GET', '/api/v1/shows');
        $request->assertJsonFragment(['id' => $show->id])
                ->assertJsonMissing(['id' => $this->show->id]);
    }

    /**
     * Test that PATCH requests ONLY update the requested data.
     *
     * @return void
     */
    public function testPatchOnlyUpdatesRequestedData()
    {
        $title = $this->show->title;
        $request = $this->json('PATCH', "/api/v1/shows/{$this->show->id}", [
            'description' => 'This is an example show description. It should be long enough to pass validation.'
        ]);

        $request->assertOk()
                ->assertJson([
                    'description' => 'This is an example show description. It should be long enough to pass validation.',
                    'off_air' => $title
                ]);
    }

    /**
     * Test that PUT requests FAIL if data is missing.
     *
     * @return void
     */
    public function testPutFailsWithMissingAttribute()
    {
        $request = $this->json('PUT', "/api/v1/shows/{$this->show->id}", [
            'title' => 'Amazing Show'
        ]);

        $request->assertStatus(422);
        $this->assertNotEquals('Amazing Show', Show::find($this->term->id)->title);
    }
}
