<?php

namespace Tests\API;

use KRLX\Show;
use KRLX\User;
use Tests\AuthenticatedTestCase;
use Illuminate\Support\Facades\Mail;

class ShowTest extends AuthenticatedTestCase
{
    public $show;
    public $host;

    public function setUp()
    {
        parent::setUp();
        Mail::fake();

        $this->show = factory(Show::class)->create(['term_id' => $this->term->id]);
        $this->host = factory(User::class)->states('carleton', 'contract_ok')->create();
        $this->show->hosts()->attach($this->host, ['accepted' => true]);
    }

    /**
     * Test that Carleton users who have signed the contract can create shows
     * using the API, but guests and unauthenticated users can't.
     *
     * @return void
     */
    public function testOnlyCarletonUsersCanCreateShows()
    {
        $data = [
            'title' => 'Gray Duck',
            'track_id' => $this->show->track_id,
            'term_id' => $this->term->id,
        ];

        $this->assertGuest('api');
        $unauthenticated_req = $this->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(401, $unauthenticated_req->getStatusCode(), "Unauthenticated user did not receive HTTP 401 when creating shows.");

        $guest_req = $this->actingAs($this->guest, 'api')->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(403, $guest_req->getStatusCode(), "Guest user did not receive HTTP 403 when creating shows.");

        $new_carl_req = $this->actingAs($this->new_carl, 'api')->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(403, $new_carl_req->getStatusCode(), "New Carleton user did not receive HTTP 403 when creating shows.");

        $carleton_req = $this->actingAs($this->carleton, 'api')->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(201, $carleton_req->getStatusCode(), "Active Carleton user did not receive HTTP 201 when creating shows.");
        $show = Show::find($carleton_req->getData()->id);
        $this->assertContains($show->id, $this->carleton->shows()->pluck('id'));
        $this->assertContains($this->carleton->id, $show->hosts()->pluck('id'));
        $this->assertNotContains($this->carleton->id, $show->invitees()->pluck('id'));

        $board_req = $this->actingAs($this->board, 'api')->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(201, $board_req->getStatusCode(), "Board user did not receive HTTP 201 when creating shows.");
        $show = Show::find($board_req->getData()->id);
        $this->assertContains($show->id, $this->board->shows()->pluck('id'));
        $this->assertContains($this->board->id, $show->hosts()->pluck('id'));
        $this->assertNotContains($this->board->id, $show->invitees()->pluck('id'));
        $this->assertTrue($show->board_boost, "Board show creation did not result in an automatic upgrade.");
    }

    /**
     * Assert that checking details about a show are available to anyone, but
     * only limited information is available if you're not a host.
     *
     * @return void
     */
    public function testAnyAccountCanCheckShows()
    {
        $unauthenticated_req = $this->json('GET', "/api/v1/shows/{$this->show->id}");
        $guest_req = $this->actingAs($this->guest, 'api')->json('GET', "/api/v1/shows/{$this->show->id}");
        $new_carl_req = $this->actingAs($this->new_carl, 'api')->json('GET', "/api/v1/shows/{$this->show->id}");
        $carleton_req = $this->actingAs($this->carleton, 'api')->json('GET', "/api/v1/shows/{$this->show->id}");
        $host_req = $this->actingAs($this->host, 'api')->json('GET', "/api/v1/shows/{$this->show->id}");
        $board_req = $this->actingAs($this->board, 'api')->json('GET', "/api/v1/shows/{$this->show->id}");

        $this->assertEmpty($this->show->content, "The content field is not empty, so JSON matching might fail.");

        $unauthenticated_req->assertStatus(401);
        $guest_req->assertOk()
                  ->assertJsonMissing(['content' => []]);
        $new_carl_req->assertOk()
                     ->assertJsonMissing(['content' => []]);
        $carleton_req->assertOk()
                     ->assertJsonMissing(['content' => []]);
        $host_req->assertOk()
                 ->assertJsonFragment(['content' => []]);
        $board_req->assertOk()
                  ->assertJsonFragment(['content' => []]);
    }

    /**
     * Test that shows can be created with an empty title and still create OK
     * (they'll have a title generated for them).
     *
     * @return void
     */
    public function testShowsCanGenerateTitles()
    {
        $request = $this->actingAs($this->host, 'api')->json('POST', '/api/v1/shows', [
            'track_id' => $this->show->track_id,
            'term_id' => $this->term->id,
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
            'track_id' => $this->show->track_id,
        ]);

        $request = $this->actingAs($this->host, 'api')->json('GET', '/api/v1/shows');
        $request->assertJsonFragment(['id' => $this->show->id])
                ->assertJsonMissing(['id' => $show->id]);
    }

    /**
     * Test that we CAN'T update a show that we're not a member of.
     *
     * @return void
     */
    public function testUpdatingSomeoneElsesSingleShow()
    {
        $request = $this->actingAs($this->carleton, 'api')->json('PATCH', "/api/v1/shows/{$this->show->id}", [
            'description' => 'This is an example show description. It should be long enough to pass validation.',
        ]);
        $this->assertNotContains($this->carleton, $this->show->hosts);
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
        $request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$this->show->id}", [
            'description' => 'This is an example show description. It should be long enough to pass validation.',
        ]);

        $request->assertOk()
                ->assertJson([
                    'description' => 'This is an example show description. It should be long enough to pass validation.',
                    'title' => $title,
                ]);
    }

    /**
     * Test that PUT requests FAIL if data is missing.
     *
     * @return void
     */
    public function testPutFailsWithMissingAttribute()
    {
        $request = $this->actingAs($this->host, 'api')->json('PUT', "/api/v1/shows/{$this->show->id}", [
            'title' => 'Amazing Show',
        ]);

        $request->assertStatus(422);
        $this->assertNotEquals('Amazing Show', Show::find($this->show->id)->title);
    }
}
