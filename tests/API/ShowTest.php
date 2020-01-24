<?php

namespace Tests\API;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use KRLX\Mail\NewUserInvitation;
use KRLX\Notifications\ShowInvitation;
use KRLX\Show;
use KRLX\Term;
use KRLX\Track;
use KRLX\User;
use Tests\AuthenticatedTestCase;

class ShowTest extends AuthenticatedTestCase
{
    use WithFaker;

    public $show;
    public $host;

    public function setUp()
    {
        parent::setUp();
        Mail::fake();
        Notification::fake();

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
        $this->assertEquals(401, $unauthenticated_req->getStatusCode(), 'Unauthenticated user did not receive HTTP 401 when creating shows.');

        $guest_req = $this->actingAs($this->guest, 'api')->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(403, $guest_req->getStatusCode(), 'Guest user did not receive HTTP 403 when creating shows.');

        $new_carl_req = $this->actingAs($this->new_carl, 'api')->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(403, $new_carl_req->getStatusCode(), 'New Carleton user did not receive HTTP 403 when creating shows.');

        $carleton_req = $this->actingAs($this->carleton, 'api')->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(201, $carleton_req->getStatusCode(), 'Active Carleton user did not receive HTTP 201 when creating shows.');
        $show = Show::find($carleton_req->getData()->id);
        $this->assertContains($show->id, $this->carleton->shows()->pluck('id'));
        $this->assertContains($this->carleton->id, $show->hosts()->pluck('id'));
        $this->assertNotContains($this->carleton->id, $show->invitees()->pluck('id'));

        $board_req = $this->actingAs($this->board, 'api')->json('POST', '/api/v1/shows', $data);
        $this->assertEquals(201, $board_req->getStatusCode(), 'Board user did not receive HTTP 201 when creating shows.');
        $show = Show::find($board_req->getData()->id);
        $this->assertContains($show->id, $this->board->shows()->pluck('id'));
        $this->assertContains($this->board->id, $show->hosts()->pluck('id'));
        $this->assertNotContains($this->board->id, $show->invitees()->pluck('id'));
        $this->assertTrue($show->board_boost, 'Board show creation did not result in an automatic upgrade.');
    }

    /**
     * Board members can create shows on closed or pending terms.
     * Verify that this is the case, as well as that non-Board users don't have
     * this ability.
     *
     * @return void
     */
    public function testBoardCreationOfShowsInOddTerms()
    {
        $pending_term = factory(Term::class)->create(['id' => '2018-PENDING', 'status' => 'pending']);
        $closed_term = factory(Term::class)->create(['id' => '2018-CLOSED', 'status' => 'closed']);

        $closed_term_show = [
            'title' => 'Gray Duck',
            'track_id' => $this->show->track_id,
            'term_id' => $closed_term->id,
        ];

        $pending_term_show = [
            'title' => 'Gray Duck',
            'track_id' => $this->show->track_id,
            'term_id' => $pending_term->id,
        ];

        foreach ([$closed_term, $pending_term] as $term) {
            $this->board->points()->create(['term_id' => $term->id, 'status' => 'provisioned']);
            $this->carleton->points()->create(['term_id' => $term->id, 'status' => 'provisioned']);
        }

        $board_closed_req = $this->actingAs($this->board, 'api')->json('POST', '/api/v1/shows', $closed_term_show);
        $board_pending_req = $this->actingAs($this->board, 'api')->json('POST', '/api/v1/shows', $pending_term_show);
        $other_closed_req = $this->actingAs($this->carleton, 'api')->json('POST', '/api/v1/shows', $closed_term_show);
        $other_pending_req = $this->actingAs($this->carleton, 'api')->json('POST', '/api/v1/shows', $pending_term_show);

        $this->assertEquals(201, $board_closed_req->getStatusCode(), 'The board member could not create a show in a closed term.');
        $this->assertEquals(201, $board_pending_req->getStatusCode(), 'The board member could not create a show in a pending term.');
        $this->assertEquals(403, $other_closed_req->getStatusCode(), 'The standard account could create a show in a closed term.');
        $this->assertEquals(403, $other_pending_req->getStatusCode(), 'The standard account could create a show in a pending term.');
    }

    /**
     * Verify that board members can update shows in closed or pending terms,
     * but other users can't.
     *
     * @return void
     */
    public function testBoardModificationOfShowsInClosedTerms()
    {
        $closed_term = factory(Term::class)->create(['status' => 'closed']);
        $pending_term = factory(Term::class)->create(['status' => 'pending']);

        $closed_term_show = factory(Show::class)->create([
            'title' => 'Gray Duck',
            'track_id' => $this->show->track_id,
            'term_id' => $closed_term->id,
        ]);
        $closed_term_show->hosts()->attach($this->carleton, ['accepted' => true]);

        $pending_term_show = factory(Show::class)->create([
            'title' => 'Gray Duck',
            'track_id' => $this->show->track_id,
            'term_id' => $closed_term->id,
        ]);
        $pending_term_show->hosts()->attach($this->carleton, ['accepted' => true]);

        foreach ([$closed_term, $pending_term] as $term) {
            $this->board->points()->create(['term_id' => $term->id, 'status' => 'provisioned']);
            $this->carleton->points()->create(['term_id' => $term->id, 'status' => 'provisioned']);
        }

        $data = ['description' => 'This is a description'];
        $board_closed_req = $this->actingAs($this->board, 'api')->json('PATCH', "/api/v1/shows/{$closed_term_show->id}", $data);
        $board_pending_req = $this->actingAs($this->board, 'api')->json('PATCH', "/api/v1/shows/{$pending_term_show->id}", $data);
        $other_closed_req = $this->actingAs($this->carleton, 'api')->json('PATCH', "/api/v1/shows/{$closed_term_show->id}", $data);
        $other_pending_req = $this->actingAs($this->carleton, 'api')->json('PATCH', "/api/v1/shows/{$pending_term_show->id}", $data);

        $this->assertEquals(200, $board_closed_req->getStatusCode(), 'The board member could not update a show in a closed term.');
        $this->assertEquals(200, $board_pending_req->getStatusCode(), 'The board member could not update a show in a pending term.');
        $this->assertEquals(403, $other_closed_req->getStatusCode(), 'The standard account could update a show in a closed term.');
        $this->assertEquals(403, $other_pending_req->getStatusCode(), 'The standard account could update a show in a pending term.');
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

        $this->assertEmpty($this->show->content, 'The content field is not empty, so JSON matching might fail.');

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

    /**
     * Test that users can delete their own shows, but others can't.
     *
     * @return void
     */
    public function testUsersCanDeleteOnlyOwnShows()
    {
        $delete_other_show = $this->actingAs($this->carleton, 'api')->json('DELETE', "/api/v1/shows/{$this->show->id}");
        $delete_my_show = $this->actingAs($this->host, 'api')->json('DELETE', "/api/v1/shows/{$this->show->id}");

        $this->assertNotContains($this->carleton, $this->show->hosts);
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
        $add_request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$this->show->id}/hosts", [
            'add' => [$this->carleton->email],
        ]);
        $add_request->assertOk();
        Notification::assertSentTo($this->carleton, ShowInvitation::class);
        $this->assertContains($this->carleton->id, $this->show->invitees->pluck('id'));
    }

    /**
     * Test the ability to invite a host who doesn't have an account.
     *
     * WARNING: This test is not thread safe; if running tests in parallel on
     *          the same database, this test must be run sequentially while all
     *          others hold.
     *
     * @return void
     */
    public function testAddingHostWithoutExistingAccount()
    {
        $faker = $this->faker();
        $email = $faker->safeEmail;

        // Since the database is sterile, we can test if new user accounts are
        // ever created (and thus, if email addresses are actually written to
        // disk) by monitoring the auto-increment value on the Users table.
        // Admittedly, this is a bit of a hack, but it works.
        // WARNING: THIS TEST IS NOT THREAD SAFE
        $first_usr = factory(User::class)->create();

        $add_request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$this->show->id}/invite", [
            'invite' => [$email],
        ]);
        $second_usr = factory(User::class)->create();

        $add_request->assertOk();
        Mail::assertQueued(NewUserInvitation::class);
        $this->assertNotContains($email, $this->show->invitees->pluck('email'));
        $this->assertEquals(1, $second_usr->id - $first_usr->id);
    }

    /**
     * Test the ability to remove a host.
     *
     * @return void
     */
    public function testRemovingHost()
    {
        $this->show->invitees()->attach($this->carleton);

        $add_request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$this->show->id}/hosts", [
            'remove' => [$this->carleton->email],
        ]);
        $add_request->assertOk();
        $this->assertNotContains($this->carleton->id, $this->show->invitees->pluck('id'));
    }

    /**
     * Test the ability to modify custom fields.
     *
     * @return void
     */
    public function testModificationOfCustomFields()
    {
        $track = factory(Track::class)->create([
            'active' => true,
            'content' => [
                ['db' => 'sponsor', 'title' => 'Sponsor', 'helptext' => null, 'type' => 'shorttext', 'rules' => ['required', 'min:3']],
            ],
        ]);
        $show = factory(Show::class)->create([
            'track_id' => $track->id,
            'term_id' => $this->term->id,
        ]);
        $show->hosts()->attach($this->host, ['accepted' => true]);

        $request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$show->id}", [
            'content' => [
                'sponsor' => 'asdf',
            ],
        ]);
        $request->assertOk();
        $testShow = Show::find($show->id);
        $this->assertEquals('asdf', $testShow->content['sponsor']);
    }

    /**
     * Test that single-occurrence shows can't simultaneously declare a day as
     * both a conflict and a preference.
     *
     * @return void
     */
    public function testOneOffShowsCantHaveSameDayAsConflictAndPreference()
    {
        $track = factory(Track::class)->create([
            'active' => true,
            'weekly' => false,
            'start_day' => $this->term->on_air->format('l'),
            'start_time' => $this->term->on_air->format('H:i'),
            'end_time' => $this->term->on_air->copy()->addHour()->format('H:i'),
        ]);
        $show = factory(Show::class)->create([
            'track_id' => $track->id,
            'term_id' => $this->term->id,
        ]);
        $show->hosts()->attach($this->host, ['accepted' => true]);

        $request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$show->id}", [
            'conflicts' => [$this->term->on_air->format('Y-m-d')],
            'preferences' => [$this->term->on_air->format('Y-m-d')],
        ]);
        $request->assertStatus(422);
        $request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$show->id}", [
            'conflicts' => [$this->term->on_air->format('Y-m-d')],
        ]);
        $request->assertStatus(200);
        $request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$show->id}", [
            'preferences' => [$this->term->on_air->format('Y-m-d')],
        ]);
        $request->assertStatus(422);
        $request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$show->id}", [
            'conflicts' => [],
            'preferences' => [$this->term->on_air->format('Y-m-d')],
        ]);
        $request->assertStatus(200);
    }

    /**
     * Test joining a show.
     *
     * @return void
     */
    public function testJoiningShow()
    {
        $request = $this->actingAs($this->carleton, 'api')->json('PUT', "/api/v1/shows/{$this->show->id}/join", [
            'token' => encrypt(['show' => $this->show->id, 'user' => $this->carleton->email]),
        ]);
        $request->assertStatus(200);
        $this->assertTrue($this->show->track->joinable);
        $this->assertNotContains($this->carleton->id, $this->show->invitees->pluck('id'));
        $this->assertContains($this->carleton->id, $this->show->hosts->pluck('id'));
    }

    /**
     * Test that "submitted" can't be edited directly via PATCH, but can be
     * when all validation rules pass and a request is sent to the submit route.
     *
     * @return void
     */
    public function testSubmissionStatusCantBeEditedWithPatch()
    {
        $show = Show::find($this->show->id);
        $this->assertFalse($show->submitted, 'The show was submitted to begin with.');

        $request = $this->actingAs($this->host, 'api')->json('PATCH', "/api/v1/shows/{$this->show->id}", [
            'description' => 'This is a show description',
            'submitted' => true,
        ]);
        $request->assertStatus(200);
        $show = Show::find($this->show->id);
        $this->assertEquals('This is a show description', $show->description, 'The description was not updated.');
        $this->assertFalse($show->submitted, 'The show was successfully submitted when it should not have been.');

        $request = $this->actingAs($this->host, 'api')->json('PUT', "/api/v1/shows/{$this->show->id}/submitted", [
            'submitted' => true,
        ]);
        $request->assertStatus(200);
        $show = Show::find($this->show->id);
        $this->assertTrue($show->submitted, 'The show was not successfully submitted when it should have been.');
    }

    /**
     * Test joining with bad tokens.
     *
     * @return void
     */
    public function testJoiningWithBadTokens()
    {
        $tokens = [
            'this is not an array',
            ['data' => 'this array is missing at least one key'],
            ['user' => 'potato', 'show' => -1],
            ['user' => $this->carleton->email, 'show' => '-_-_-_'],
        ];

        foreach ($tokens as $token) {
            $request = $this->actingAs($this->carleton, 'api')->json('PUT', "/api/v1/shows/{$this->show->id}/join", [
                'token' => encrypt($token),
            ]);
            $request->assertStatus(400);
        }
    }

    /**
     * Test that adding a "cancel" parameter to the join message removes the
     * invitation altogether.
     *
     * @return void
     */
    public function testCancellingInvitation()
    {
        $this->show->invitees()->attach($this->carleton->id);
        $this->assertContains($this->carleton->id, $this->show->invitees()->pluck('id'));
        $this->assertNotContains($this->carleton->id, $this->show->hosts()->pluck('id'));

        $request = $this->actingAs($this->carleton, 'api')->json('PUT', "/api/v1/shows/{$this->show->id}/join", [
            'token' => encrypt(['user' => $this->carleton->email, 'show' => $this->show->id]),
            'cancel' => true,
        ]);
        $request->assertOk();
        $this->assertNotContains($this->carleton->id, $this->show->invitees()->pluck('id'), 'The user was not removed from the invitee list.');
        $this->assertNotContains($this->carleton->id, $this->show->hosts()->pluck('id'), 'The user was added to the host list when they should not have been.');
    }

    /**
     * Test that remind-show emails are NOT sent if the term does not have an
     * application closure date set.
     *
     * @return void
     */
    public function testShowReminderEmailsDontSendWithoutCloseDate()
    {
        $term = factory(Term::class)->create(['applications_close' => null, 'status' => 'active']);
        $show = factory(Show::class)->create([
            'term_id' => $term->id,
            'track_id' => $this->show->track_id,
            'submitted' => false,
        ]);
        $request = $this->actingAs($this->board, 'api')->json('POST', '/api/v1/shows/remind', [
            'term_id' => $term->id,
        ]);
        $this->assertFalse($show->submitted);

        Mail::assertNotQueued(ShowReminder::class);
    }
}
