<?php

namespace Tests\Feature;

use KRLX\Show;
use KRLX\Term;
use Tests\AuthenticatedTestCase;

class ContractTest extends AuthenticatedTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->carleton->points()->delete();
    }

    /**
     * Verify that users who have not signed the contract yet for the current
     * term are redirected to do so before they can do most show operations.
     *
     * @return void
     */
    public function testUsersMustSignContract()
    {
        $show = factory(Show::class)->create([
            'term_id' => $this->term->id,
        ]);

        $direct_request = $this->actingAs($this->carleton)->get('/shows/join');
        $show_join_request = $this->actingAs($this->carleton)->get("/shows/join/{$show->id}");

        $this->assertCount(0, $this->carleton->points->where('term_id', $this->term->id));
        $direct_request->assertRedirect('/contract');
        $show_join_request->assertRedirect('/contract')
                          ->assertSessionHas('term', $this->term->id);
    }

    /**
     * Verify that viewing the contract directly is allowed, and that the
     * correct Term ID appears.
     *
     * @return void
     */
    public function testCorrectTermAppears()
    {
        $request = $this->actingAs($this->carleton)->get('/contract');
        $request->assertOk()
                ->assertSessionMissing('term')
                ->assertSee($this->term->id);
    }

    /**
     * Verify that signing the contract successfully provisions an experience
     * point, if (and only if) one does not already exist for the term.
     *
     * @return void
     */
    public function testSigningContractProvisionsOnePoint()
    {
        $user_request = $this->actingAs($this->carleton)->post('/contract', [
            'term' => $this->term->id,
            'contract' => 'true'
        ]);

        $this->assertCount(1, $this->board->points->where('term_id', $this->term->id));
        $board_request = $this->actingAs($this->board)->post('/contract', [
            'term' => $this->term->id,
            'contract' => 'true'
        ]);

        $this->assertCount(1, $this->carleton->points->where('term_id', $this->term->id));
        $this->assertCount(1, $this->board->points->where('term_id', $this->term->id));
        $user_request->assertRedirect('/home');
    }
}
