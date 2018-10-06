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
        $this->show->hosts()->attach($this->board, ['accepted' => true]);
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
}
