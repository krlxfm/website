<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use KRLX\Show;
use KRLX\Term;
use KRLX\User;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    public $show;
    public $term;
    public $user;
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->term = factory(Term::class)->create();
        $this->user = factory(User::class)->create();
        $this->show = factory(Show::class)->create([
            'term_id' => $this->term->id,
        ]);
        $this->session = $this->actingAs($this->user);
        $this->artisan('db:seed');
    }

    /**
     * Test that we can view the contract page directly without going through
     * the middleware first.
     *
     * @return void
     */
    public function testDirectContractViewing()
    {
        $request = $this->get('/contract');

        $request->assertSessionMissing('term')
                ->assertOk()
                ->assertViewIs('legal.contract')
                ->assertSee($this->term->id);
    }
}
