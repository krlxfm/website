<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use KRLX\Show;
use KRLX\Term;
use KRLX\Track;
use KRLX\User;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public $show;
    public $track;
    public $term;
    public $user;
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('db:seed');
        $this->track = factory(Track::class)->create([
            'active' => true,
        ]);
        $this->term = factory(Term::class)->create([
            'status' => 'active',
        ]);
        $this->user = factory(User::class)->states('contract_ok')->create();
        $this->show = factory(Show::class)->create([
            'id' => 'SHOW01',
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => false,
        ]);
        $this->show->hosts()->attach($this->user, ['accepted' => true]);
        $this->session = $this->actingAs($this->user);
    }

    /**
     * Test that we can see our list of incomplete shows.
     *
     * @return void
     */
    public function testUserCanSeeOwnShows()
    {
        $secondShow = factory(Show::class)->create([
            'id' => 'SECOND',
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => false,
        ]);
        $secondShow->hosts()->attach($this->user, ['accepted' => true]);

        $request = $this->get('/shows');

        $this->assertCount(1, Term::all());
        $request->assertOk()
                ->assertSeeInOrder(['Applications in progress', $this->show->title, $secondShow->title, 'Completed applications']);
    }

    /**
     * Test that the list of available tracks appears when we go to create a
     * new show.
     *
     * @return void
     */
    public function testTrackListAppearsWhenCreatingNewShow()
    {
        $track = factory(Track::class)->create([
            'active' => false,
        ]);
        $request = $this->get('/shows/create');

        $request->assertOk()
                ->assertSee($this->track->name)
                ->assertDontSee($track->name);
    }

    /**
     * Test that submitting the show creation form on the web results in a new
     * show being created, and that we are redirected there.
     *
     * @return void
     */
    public function testWebShowCreation()
    {
        $request = $this->post('/shows', [
            'title' => 'Example Show Title',
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
        ]);

        $show = Show::where('title', 'Example Show Title')->first();
        $this->assertContains($this->user->id, $show->hosts()->pluck('id'));
        $this->assertFalse($this->user->can('auto-request Zone S'));
        $this->assertFalse($show->boosted, 'A show was boosted when it should not have been.');
        $request->assertRedirect(route('shows.hosts', $show->id));
    }

    /**
     * Test that a user with permission to automatically request Zone S will
     * automatically have their FIRST show get upgraded.
     *
     * @return void
     */
    public function testAutomaticUpgradeRequest()
    {
        $this->user->givePermissionTo('auto-request Zone S');

        $this->post('/shows', [
            'title' => 'Example Show Title',
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
        ]);
        $upgraded_show = Show::where('title', 'Example Show Title')->first();
        $this->post('/shows', [
            'title' => 'Other Show Title',
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
        ]);
        $non_upgraded_show = Show::where('title', 'Other Show Title')->first();

        $this->assertTrue($upgraded_show->boosted, 'Failed asserting that the show is boosted.');
        $this->assertTrue($upgraded_show->board_boost, 'Failed asserting that the show has a Board Upgrade Certificate.');
        $this->assertFalse($non_upgraded_show->boosted, 'A second show was upgraded when it should not have been.');
        $this->assertFalse($non_upgraded_show->board_boost, 'A second Board Upgrade Certificate was issued.');
    }

    /**
     * Test that we can't create a show with expletives in the title.
     *
     * @return void
     */
    public function testWebShowCreationWithExpletives()
    {
        $title = config('defaults.banned_words.full')[0]." except this time it's a radio show";
        $request = $this->post('/shows', [
            'title' => $title,
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
        ]);

        $show = Show::where('title', $title)->first();
        $this->assertNull($show);
    }

    /**
     * Test that we have access to the hosts view after creating a show.
     *
     * @return void
     */
    public function testHostsViewRenders()
    {
        $request = $this->get("/shows/{$this->show->id}/hosts");

        $request->assertOk()
                ->assertViewIs('shows.hosts');
    }

    /**
     * Test that we have access to the content view after creating a show, and
     * that the view marks the "description" field as required.
     *
     * @return void
     */
    public function testContentViewRenders()
    {
        $request = $this->get("/shows/{$this->show->id}/content");

        $this->assertEquals(0, strlen($this->show->description ?? ''));
        $request->assertOk()
                ->assertViewIs('shows.content')
                ->assertSee('The description field is required.');
    }

    /**
     * Test that we have access to the schedule view after creating a show.
     *
     * @return void
     */
    public function testScheduleViewRenders()
    {
        $request = $this->get("/shows/{$this->show->id}/schedule");

        $request->assertOk()
                ->assertViewIs('shows.schedule');
    }

    /**
     * Test that we have access to the review view after creating a show.
     *
     * @return void
     */
    public function testReviewScreenRenders()
    {
        $request = $this->get("/shows/{$this->show->id}");

        $request->assertOk()
                ->assertViewIs('shows.review')
                ->assertSee($this->show->term->name);
    }

    /**
     * Test that tracks with custom fields can still render okay.
     *
     * @return void
     */
    public function testCustomFieldTracksStillRender()
    {
        $track = factory(Track::class)->create([
            'active' => true,
            'content' => [
                ['db' => 'sponsor', 'title' => 'Sponsor', 'helptext' => null, 'type' => 'shorttext', 'rules' => ['required', 'min:3']],
            ],
        ]);
        $show = factory(Show::class)->create([
            'term_id' => $this->term->id,
            'track_id' => $track->id,
        ]);
        $show->hosts()->attach($this->user->id, ['accepted' => true]);

        $request = $this->get("/shows/{$show->id}/content");

        $request->assertOk()
                ->assertViewIs('shows.content')
                ->assertSee('Sponsor');
    }

    /**
     * Test that we have access to the Join Shows screen without an ID.
     *
     * @return void
     */
    public function testFindShowViewRenders()
    {
        $request = $this->get('/shows/join');

        $request->assertOk()
                ->assertViewIs('shows.find');
    }

    /**
     * Test that we have access to the join view, assuming we're not a host of
     * the show we're trying to join.
     *
     * @return void
     */
    public function testJoinShowViewRenders()
    {
        $show = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
        ]);
        $show->invitees()->attach($this->user);
        $request = $this->get("/shows/join/{$show->id}");

        $request->assertOk()
                ->assertViewIs('shows.join')
                ->assertSee($show->title);
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

        $request = $this->put("/shows/join/{$show->id}", [
            'token' => encrypt(['show' => $show->id, 'user' => $this->user->email]),
        ]);
        $request->assertRedirect(route('shows.schedule', $show))
                ->assertSessionHas('success');
    }
}
