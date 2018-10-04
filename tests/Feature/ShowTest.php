<?php

namespace Tests\Feature;

use KRLX\Term;
use KRLX\Show;
use Tests\AuthenticatedTestCase;

class ShowTest extends AuthenticatedTestCase
{
    public $my_show;
    public $other_show;

    public function setUp()
    {
        parent::setUp();

        $this->my_show = factory(Show::class)->create(['term_id' => $this->term->id]);
        $this->other_show = factory(Show::class)->create(['term_id' => $this->term->id]);

        $this->my_show->hosts()->attach($this->carleton, ['accepted' => true]);
        $this->other_show->hosts()->attach($this->board, ['accepted' => true]);
    }

    /**
     * Test that My Shows displays the titles of the shows that the requesting
     * user is actually a part of.
     *
     * @return void
     */
    public function testMyShowsReturnsOnlyMyShow()
    {
        $carl_request = $this->actingAs($this->carleton)->get('/shows');
        $board_request = $this->actingAs($this->board)->get('/shows');

        $carl_request->assertOk()
                     ->assertSee($this->my_show->title)
                     ->assertDontSee($this->other_show->title);
        $board_request->assertOk()
                      ->assertSee($this->other_show->title)
                      ->assertDontSee($this->my_show->title);
    }

    /**
     * Test that Board members (who can override Pending and Closed terms) see
     * those options when creating a show.
     *
     * @return void
     */
    public function testBoardMembersCanSeeClosedTerms()
    {
        $pending_term = factory(Term::class)->create([
            'status' => 'pending',
            'on_air' => $this->term->on_air->subWeek(),
        ]);
        $closed_term = factory(Term::class)->create([
            'status' => 'closed',
            'on_air' => $this->term->on_air->subWeek(),
        ]);

        $carl_request = $this->actingAs($this->carleton)->get('/shows/create');
        $board_request = $this->actingAs($this->board)->get('/shows/create');

        $carl_request->assertOk();
        $this->assertCount(1, $carl_request->baseResponse->original->terms);
        $board_request->assertOk();
        $this->assertCount(3, $board_request->baseResponse->original->terms);
    }

    /**
     * Test that shows can be created.
     *
     * @return void
     */
    public function testWebShowCreation()
    {
        $request = $this->actingAs($this->carleton)->post('/shows', [
            'term_id' => $this->term->id,
            'track_id' => $this->my_show->track_id,
            'title' => 'Test Show',
        ]);

        $show = Show::where('title', 'Test Show')->first();

        $this->assertContains($this->carleton->id, $show->hosts->pluck('id'));
        $request->assertRedirect("/shows/{$show->id}/hosts");
    }

    /**
     * Test that shows can be deleted.
     *
     * @return void
     */
    public function testWebShowDeletion()
    {
        $request = $this->actingAs($this->carleton)->delete("/shows/{$this->my_show->id}");

        $show = Show::find($this->my_show->id);
        $this->assertNull($show);
    }
}
