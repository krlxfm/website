<?php

namespace Tests\API;

use KRLX\Show;
use KRLX\Term;
use KRLX\User;
use KRLX\Track;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends APITestCase
{
    use RefreshDatabase, WithFaker;

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
            'track_id' => $this->track->id,
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
            'term_id' => $this->term->id,
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
            'track_id' => $this->track->id,
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
             'track_id' => $this->track->id,
         ]);

        $request = $this->json('PATCH', "/api/v1/shows/{$show->id}", [
             'description' => 'This is an example show description. It should be long enough to pass validation.',
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
        $request = $this->json('PUT', "/api/v1/shows/{$this->show->id}", [
            'title' => 'Amazing Show',
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
            'track_id' => $this->track->id,
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
            'add' => [$new_host->email],
        ]);
        $add_request->assertOk();
        $this->assertContains($new_host->id, $this->show->invitees->pluck('id'));
    }

    /**
     * Test the ability to invite a host who doesn't have an account.
     *
     * @return void
     */
    public function testAddingHostWithoutExistingAccount()
    {
        $faker = $this->faker();
        $email = $faker->safeEmail;

        $add_request = $this->json('PATCH', "/api/v1/shows/{$this->show->id}/invite", [
            'invite' => [$email],
        ]);
        $add_request->assertOk();
        $this->assertNotContains($email, $this->show->invitees->pluck('email'));
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
            'remove' => [$new_host->email],
        ]);
        $add_request->assertOk();
        $this->assertNotContains($new_host->id, $this->show->invitees->pluck('id'));
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
        $show->hosts()->attach($this->user, ['accepted' => true]);

        $request = $this->json('PATCH', "/api/v1/shows/{$show->id}", [
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
        $show->hosts()->attach($this->user, ['accepted' => true]);

        $request = $this->json('PATCH', "/api/v1/shows/{$show->id}", [
            'conflicts' => [$this->term->on_air->format('Y-m-d')],
            'preferences' => [$this->term->on_air->format('Y-m-d')],
        ]);
        $request->assertStatus(422);
        $request = $this->json('PATCH', "/api/v1/shows/{$show->id}", [
            'conflicts' => [$this->term->on_air->format('Y-m-d')],
        ]);
        $request->assertStatus(200);
        $request = $this->json('PATCH', "/api/v1/shows/{$show->id}", [
            'preferences' => [$this->term->on_air->format('Y-m-d')],
        ]);
        $request->assertStatus(422);
        $request = $this->json('PATCH', "/api/v1/shows/{$show->id}", [
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
        $show = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
        ]);
        $show->hosts()->attach($this->user, ['accepted' => true]);

        $request = $this->json('PUT', "/api/v1/shows/{$show->id}/join", [
            'token' => encrypt(['show' => $show->id, 'user' => $this->user->email]),
        ]);
        $request->assertStatus(200);
        $this->assertNotContains($this->user->id, $show->invitees->pluck('id'));
        $this->assertContains($this->user->id, $show->hosts->pluck('id'));
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

        $request = $this->json('PATCH', "/api/v1/shows/{$this->show->id}", [
            'description' => 'This is a show description',
            'submitted' => true,
        ]);
        $request->assertStatus(200);
        $show = Show::find($this->show->id);
        $this->assertEquals('This is a show description', $show->description, 'The description was not updated.');
        $this->assertFalse($show->submitted, 'The show was successfully submitted when it should not have been.');

        $request = $this->json('PUT', "/api/v1/shows/{$this->show->id}/submitted", [
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
        $show = factory(Show::class)->create();
        $tokens = [
            'this is not an array',
            ['data' => 'this array is missing at least one key'],
            ['user' => 'potato', 'show' => -1],
            ['user' => $this->user->id, 'show' => '-_-_-_'],
        ];

        foreach($tokens as $token) {
            $request = $this->json('PUT', "/api/v1/shows/{$show->id}/join", [
                'token' => encrypt($token),
            ]);
            $request->assertStatus(400);
        }
    }
}
