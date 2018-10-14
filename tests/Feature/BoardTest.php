<?php

namespace Tests\Feature;

use KRLX\User;
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
        $more_board = factory(User::class, 5)->states('board')->create();
        $users = $more_board->concat($this->board);

        $request = $this->actingAs($this->carleton)->get('/board');
        $request->assertOk();

        foreach ($users as $user) {
            $request->assertSeeInOrder([$user->full_name, $user->bio]);
        }
    }
}
