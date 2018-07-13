<?php

namespace Tests\Feature;

use KRLX\User;
use KRLX\Show;
use KRLX\Track;
use KRLX\Term;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        $this->user = factory(User::class)->create();
        $this->track = factory(Track::class)->create([
            'active' => true
        ]);
        $this->term = factory(Term::class)->create([
            'accepting_applications' => true
        ]);
        $this->show = factory(Show::class)->create([
            'id' => 'SHOW01',
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => false
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
            'submitted' => false
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
            'active' => false
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
            'term_id' => $this->term->id
        ]);

        $show = Show::where('title', 'Example Show Title')->first();
        $this->assertContains($this->user->id, $show->hosts()->pluck('id'));
        $request->assertRedirect(route('shows.hosts', $show->id));
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
     * Test that we have access to the content view after creating a show.
     *
     * @return void
     */
    public function testContentViewRenders()
    {
        $request = $this->get("/shows/{$this->show->id}/content");

        $request->assertOk()
                ->assertViewIs('shows.content');
    }
}
