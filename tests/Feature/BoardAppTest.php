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

        $request = $this->actingAs($this->board)->get('/board/apply/start');
        $request->assertRedirect();
        $this->assertCount(1, $this->board->board_apps->where('year', date('Y')), 'The board application was not created when it should have been');

        $request = $this->actingAs($this->board)->get('/board/apply/start');
        $request->assertRedirect();
        $this->assertCount(1, $this->board->board_apps->where('year', date('Y')), 'A second board application was created when it should not have been');
    }
}
