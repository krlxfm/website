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
            'track_id' => $this->track->id,
            'term_id' => $this->term->id,
            'submitted' => false
        ]);
        $secondShow->hosts()->attach($this->user, ['accepted' => true]);

        $request = $this->get('/shows');

        $request->assertOk()
                ->assertSeeInOrder(['Applications in progress', $this->show->title, $secondShow->title, 'Completed applications']);
    }
}
