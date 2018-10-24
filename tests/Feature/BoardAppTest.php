<?php

namespace Tests\Feature;

use KRLX\Config;
use Carbon\Carbon;
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

    /**
     * Test the logistics view: it should render, and we should see ALL of the
     * dates/times listed in the config database.
     *
     * @return void
     */
    public function testLogisticsViewRenders()
    {
        $this->board->board_apps()->create();
        $year = date('Y');

        $interview_options = json_decode(Config::valueOr('interview options', '[]'), true);
        $opts = [];
        foreach($interview_options as $option) {
            $start = Carbon::parse($option['date'].' '.$option['start'].':00');
            $end = Carbon::parse($option['date'].' '.$option['end'].':00');
            $time = $start->copy();
            while ($time < $end) {
                // What is this? We use non-breaking space (NBSP) characters to
                // force the display to work as we intend on small screens.
                // Because assertSeeInOrder scans literal text (not rendered),
                // we'll need to test that the non-breaking spaces are present.
                $opts[] = str_replace(' ', '&nbsp;', $time->format('D, M j,')).' '.str_replace(' ', '&nbsp;', $time->format('g:i a'));
                $time->addMinutes(15);
            }
        }

        $req = $this->actingAs($this->board)->get("/board/apply/$year/logistics");

        $this->assertNotEmpty($opts);
        $req->assertOk();
        $req->assertSeeInOrder($opts);
    }
}
