<?php

namespace Tests\Feature;

use KRLX\BoardApp;
use Tests\AuthenticatedTestCase;

class BoardAppTest extends AuthenticatedTestCase
{
    /**
     * Test that the base "Apply" view renders.
     *
     * @return void
     */
    public function testBaseApplyViewRenders()
    {
        $request = $this->actingAs($this->board)->get('/board/apply');
        $request->assertOk()
                ->assertSee(date('Y'));
    }

    /**
     * Test that creating a Board application redirects the user there.
     * Duplicate applications within the same year do not result in a second
     * application being created.
     *
     * @return void
     */
    public function testBoardAppCreation()
    {
        $this->assertCount(0, $this->board->board_apps->where('year', date('Y')), 'The board user starts off with a board application');

        $req1 = $this->actingAs($this->board)->get('/board/apply/start');
        $req1->assertRedirect();
        $this->assertEquals(1, $this->board->board_apps()->where('year', date('Y'))->count(), 'The board application was not created when it should have been');

        $req2 = $this->actingAs($this->board)->get('/board/apply/start');
        $req2->assertRedirect();
        $this->assertEquals(1, $this->board->board_apps()->where('year', date('Y'))->count(), 'A second board application was created when it should not have been');
    }

    /**
     * Verify that garbage strings don't count as board applications.
     *
     * @return void
     */
    public function testGarbageStringsDontCountAsApplicationSearchTerms()
    {
        $this->board->board_apps()->create();
        $year = date('Y');

        $req1 = $this->actingAs($this->board)->get('/board/apply/asdf');
        $req2 = $this->actingAs($this->board)->get('/board/apply/2000');
        $req3 = $this->actingAs($this->board)->get("/board/apply/$year");

        $req1->assertStatus(302);
        $req2->assertStatus(302);
        $req3->assertStatus(200);
    }
}
