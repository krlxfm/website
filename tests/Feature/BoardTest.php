<?php

namespace Tests\Feature;

use Tests\AuthenticatedTestCase;

class BoardTest extends AuthenticatedTestCase
{
    /**
     * Test that visitng the "Meet the Board" page displays all users who are
     * members of the board.
     *
     * @return void
     */
    public function testMeetingTheBoard()
    {
        $request = $this->actingAs($this->carleton)->get('/board');
        $request->assertOk();
        $request->assertSeeInOrder([e($this->board->full_name), $this->board->bio]);
    }
}
