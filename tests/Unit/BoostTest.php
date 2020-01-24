<?php

namespace Tests\Unit;

use KRLX\Boost;
use KRLX\Show;
use Tests\UnitBaseCase;

class BoostTest extends UnitBaseCase
{
    public $boost;

    public function setUp()
    {
        parent::setUp();

        $this->boost = $this->user->boosts()->create(['type' => 'S']);
    }

    /**
     * Assert that a boost has a user attached to it.
     *
     * @return void
     */
    public function testBoostsHaveOwners()
    {
        $this->assertEquals($this->user->id, $this->boost->user->id);
    }

    /**
     * Test when boosts can be transferred. Some boosts are "free agents" until
     * they have been assigned to a term, at which point they can't leave that
     * term. Other boosts are assigned to a term from the beginning.
     *
     * @return void
     */
    public function testAssigningShowToBoost()
    {
        $bad_show = Show::find('SHOW-01');
        $term_mismatch_show = factory(Show::class)->create();
        $this_term_show = factory(Show::class)->create(['term_id' => $this->term->id]);
        $term_mismatch_boost = $this->user->boosts()->create(['type' => 'zone', 'term_id' => $this->term->id]);
        $show_mismatch_boost = $this->user->boosts()->create(['type' => 'zone', 'show_id' => $this_term_show->id]);

        // Sanity check the arrangement
        $this->assertNull($bad_show);
        $this->assertNull($term_mismatch_boost->show_id);
        $this->assertNull($show_mismatch_boost->term_id);
        $this->assertNotEquals($this->term->id, $term_mismatch_show->term->id);

        // Condition 1: Transfer/assignment should fail for non-existent shows.
        $this->boost->show_id = 'SHOW-01';
        $this->boost->save();
        $this->assertNull($this->boost->show_id);

        // Condition 2: Term mismatch should cause the transfer to fail.
        $term_mismatch_boost->show_id = $term_mismatch_show->id;
        $term_mismatch_boost->save();
        $this->assertNull($term_mismatch_boost->show_id);

        // Condition 3: If the term object doesn't have a Term ID on it, it's
        // still restricted (this prevents certificates from being pulled ahead
        // when they shouldn't).
        $show_mismatch_boost->show_id = $term_mismatch_show->id;
        $show_mismatch_boost->save();
        $this->assertEquals($this_term_show->id, $show_mismatch_boost->show_id);
    }
}
