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
        $this->show->hosts()->attach($this->user->id, ['accepted' => true]);
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

        $request = $this->json('GET', '/api/v1/shows');
        $request->assertJsonFragment(['id' => $this->show->id])
                ->assertJsonMissing(['id' => $show->id]);
    }

    /**
     * Test that we can query a single show.
     *
     * @return void
     */
    public function testQueryingSingleShow()
    {
        $request = $this->json('GET', "/api/v1/shows/{$this->show->id}");

        $request->assertOk()
                ->assertJson(['id' => $this->show->id]);
    }

    /**
     * Test that we CAN'T update a show that we're not a member of.
     *
     * @return void
     */
     public function testUpdatingSomeoneElsesSingleShow()
     {
         $show = factory(Show::class)->create([
             'term_id' => $this->term->id,
             'track_id' => $this->track->id
         ]);

         $request = $this->json('PATCH', "/api/v1/shows/{$show->id}", [
             'description' => 'This is an example show description. It should be long enough to pass validation.'
         ]);
         $this->assertNotContains($this->user, $show->hosts);
         $request->assertStatus(403);
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
                    'title' => $title
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
        $this->assertNotEquals('Amazing Show', Show::find($this->show->id)->title);
    }

    /**
     * Test that users can delete their own shows.
     *
     * @return void
     */
    public function testUsersCanDeleteOnlyOwnShows()
    {
        $show = factory(Show::class)->create([
            'term_id' => $this->term->id,
            'track_id' => $this->track->id
        ]);

        $delete_my_show = $this->json('DELETE', "/api/v1/shows/{$this->show->id}");
        $delete_other_show = $this->json('DELETE', "/api/v1/shows/{$show->id}");

        $this->assertNotContains($this->user, $show->hosts);
        $delete_my_show->assertStatus(204);
        $delete_other_show->assertStatus(403);
    }

    /**
     * Test the ability to add a host.
     *
     * @return void
     */
    public function testAddingHost()
    {
        $new_host = factory(User::class)->create();

        $add_request = $this->json('PATCH', "/api/v1/shows/{$this->show->id}/hosts", [
            'add' => [$new_host->email]
        ]);
        $add_request->assertOk();
        $this->assertContains($new_host->id, $this->show->invitees->pluck('id'));
    }

    /**
     * Test the ability to remove a host.
     *
     * @return void
     */
    public function testRemovingHost()
    {
        $new_host = factory(User::class)->create();
        $this->show->invitees()->attach($new_host);

        $add_request = $this->json('PATCH', "/api/v1/shows/{$this->show->id}/hosts", [
            'remove' => [$new_host->email]
        ]);
        $add_request->assertOk();
        $this->assertNotContains($new_host->id, $this->show->invitees->pluck('id'));
    }
}
