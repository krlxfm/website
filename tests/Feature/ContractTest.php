<?php

namespace Tests\Feature;

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
        $this->assertCount(0, $this->carleton->points->where('term_id', $this->term->id));

        $request = $this->actingAs($this->carleton)->get('/shows/join');
        $request->assertRedirect('/contract');
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
                ->assertSee($this->term->id);
    }
}
