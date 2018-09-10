<?php

namespace Tests\Feature;

use KRLX\Show;
use KRLX\Term;
use KRLX\User;
use KRLX\Track;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowSupportTest extends TestCase
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
     * Test that "All Shows" shows *all* shows, even those which do not include
     * the requesting user, and those which have not been submitted yet.
     *
     * @return void
     */
    public function testAllShowsReturnsAllShows()
    {
        $show = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => true,
        ]);

        $request = $this->get('/shows/all');
        $request->assertOk();
        $request->assertSeeInOrder([$show->title, $this->show->title]);
    }

    /**
     * Test that "All DJs" shows all hosts on submitted shows.
     *
     * @return void
     */
    public function testAllDJsReturnsAllUsers()
    {
        $user = factory(User::class)->create();
        $show = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => false,
        ]);

        $this->show->submitted = true;
        $this->show->invitees()->attach($user);
        $show->hosts()->attach($this->user, ['accepted' => true]);
        $this->show->save();

        $request = $this->get('/shows/djs');
        $request->assertOk()
                ->assertSee(e($this->user->name))
                ->assertDontSee(e($user->name))
                ->assertDontSee($show->title);
    }

    /**
     * Test that "All Shows" sorts by track order first, then priority, then
     * submission time.
     *
     * @return void
     */
    public function testAllShowsSortOrder()
    {
        $high_priority_user = factory(User::class)->create([
            'year' => date('Y') + 2,
        ]);
        $mid_priority_user = factory(User::class)->create([
            'year' => date('Y') + 1,
        ]);
        $high_priority_track = factory(Track::class)->create([
            'active' => true,
            'order' => 900,
        ]);
        $high_priority_show = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'priority' => 'A3',
            'submitted' => true,
            'updated_at' => Carbon::now()->subMinutes(20),
        ]);
        $recent_show = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => true,
            'updated_at' => Carbon::now()->subMinutes(10),
        ]);
        $high_track_show = factory(Show::class)->create([
            'track_id' => $high_priority_track->id,
            'term_id' => $this->term->id,
            'submitted' => true,
            'updated_at' => Carbon::now()->subMinutes(30),
        ]);

        $high_priority_show->hosts()->attach($high_priority_user, ['accepted' => true]);
        $recent_show->hosts()->attach($high_priority_user, ['accepted' => true]);
        $high_track_show->hosts()->attach($mid_priority_user, ['accepted' => true]);

        $shows = $this->term->shows()->where('submitted', true)->get();

        $request = $this->get('/shows/all');
        $request->assertOk();
        $request->assertSeeInOrder([$high_track_show->title, $high_priority_show->title, $recent_show->title]);
    }

    /**
     * Test that shows on tracks with an order of 0 appear in the All Shows view
     * after the submitted shows, but before incomplete ones.
     *
     * @return void
     */
    public function testShowsOnZeroOrderTracksDontAppearInRoster()
    {
        $show = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => true,
        ]);
        $show->hosts()->attach($this->user->id);
        $one_off_track = factory(Track::class)->create([
            'active' => true,
            'weekly' => false,
            'order' => 0,
            'start_day' => 'Sunday',
            'start_time' => '17:00',
            'end_time' => '19:00',
        ]);
        $one_off_show = factory(Show::class)->create([
            'track_id' => $one_off_track->id,
            'term_id' => $this->term->id,
            'submitted' => true,
        ]);
        $one_off_show->hosts()->attach($this->user->id);

        $request = $this->get("/shows/all/{$this->show->term->id}");
        $request->assertOk()
                ->assertSeeInOrder([$show->title, $one_off_track->name, $one_off_show->title, 'Incomplete Shows', $this->show->title]);
    }

    /**
     * Test that shows from old terms don't appear in the All DJs view.
     *
     * @return void
     */
    public function testOldShowsDontAppearInRoster()
    {
        $show = factory(Show::class)->create([
            'track_id' => $this->track->id,
            'submitted' => true,
        ]);
        $show->hosts()->attach($this->user->id);
        $this->show->submitted = true;
        $this->show->save();

        $this->assertNotEquals($show->term->id, $this->show->term->id);
        $request = $this->get("/shows/djs/{$this->show->term->id}");
        $request->assertOk()
                ->assertSee($this->show->title)
                ->assertDontSee($show->title);
    }
}
