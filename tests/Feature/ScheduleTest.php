<?php

namespace Tests\Feature;

use KRLX\Term;
use KRLX\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    public $user;

    public function setUp()
    {
        parent::setUp();

        $this->term = factory(Term::class)->create();
        $this->user = factory(User::class)->states('contract_ok')->create();
    }

    /**
     * Test that a user (with signed contracts) has access to the schedules.
     *
     * @return void
     */
    public function testUserHasAccessToScheduleView()
    {
        $request = $this->actingAs($this->user)->get("/schedule/build/{$this->term->id}");
        $request->assertOk();
    }
}
