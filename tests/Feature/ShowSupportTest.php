<?php

namespace Tests\Feature;

use KRLX\Show;
use KRLX\Term;
use KRLX\User;
use KRLX\Track;
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

        $this->user = factory(User::class)->create();
        $this->track = factory(Track::class)->create([
            'active' => true,
        ]);
        $this->term = factory(Term::class)->create([
            'accepting_applications' => true,
        ]);
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
        $request->assertSeeInOrder([$show->title, $this->show->title]);
    }
}
