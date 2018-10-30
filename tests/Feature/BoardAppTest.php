<?php

namespace Tests\Feature;

use KRLX\User;
use KRLX\Config;
use KRLX\Position;
use Carbon\Carbon;
use KRLX\BoardApp;
use Tests\AuthenticatedTestCase;

class BoardAppTest extends AuthenticatedTestCase
{
    public $user;
    public $board_app;

    public function setUp()
    {
        parent::setUp();

        $user = factory(User::class)->states('carleton', 'contract_ok', 'board')->create();
        $this->user = $user;
        $this->board_app = $user->board_apps()->create();
    }

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
     * Test that the "Positions" view renders.
     *
     * @return void
     */
    public function testPositionsViewRenders()
    {
        $request = $this->actingAs($this->board)->get('/board/positions');
        $request->assertOk()
                ->assertSeeInOrder(Position::orderBy('order')->get()->pluck('title')->all());
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
        $req1 = $this->actingAs($this->user)->get('/board/apply/asdf');
        $req2 = $this->actingAs($this->user)->get('/board/apply/2000');
        $req3 = $this->actingAs($this->user)->get("/board/apply/{$this->board_app->year}");

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

        $req = $this->actingAs($this->user)->get("/board/apply/{$this->board_app->year}/logistics");

        $this->assertNotEmpty($opts);
        $req->assertOk();
        $req->assertSeeInOrder($opts);
    }

    /**
     * Test valid input to the Logistics page.
     *
     * @return void
     */
    public function testLogisticsSubmission()
    {
        $data = [
            'interview_schedule' => $this->board_app->interview_schedule,
            'remote' => false,
            'remote_contact' => null,
            'remote_platform' => null,
            'ocs' => 'none',
        ];

        foreach($this->board_app->interview_schedule as $date => $value) {
            $data['interview_schedule'][$date] = 3;
        }

        $req = $this->actingAs($this->user)->patch("/board/apply/{$this->board_app->year}");
        $req->assertRedirect(route('board.app', $this->board_app->year));
    }

    /**
     * Test that the Common Questions view loads.
     *
     * @return void
     */
    public function testCommonViewLoads()
    {
        $req = $this->actingAs($this->user)->get("/board/apply/{$this->board_app->year}/common");
        $req->assertOk()
            ->assertSeeInOrder(collect($this->board_app->common)->keys()->all());
    }

    /**
     * Test that we can add a standard (non-restricted) position to a board app.
     *
     * @return void
     */
    public function testAddingStandardPosition()
    {
        $position = factory(Position::class)->create();

        $req = $this->actingAs($this->user)->post("/board/apply/positions", ['position_id' => $position->id, 'board_app_id' => $this->board_app->id]);
        $req->assertRedirect();
        $this->assertCount(1, $this->board_app->positions);
    }
}
