<?php

namespace Tests\Unit;

use KRLX\Term;
use KRLX\User;
use Tests\UnitBaseCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends UnitBaseCase
{
    use WithFaker;

    /**
     * Test the "Full Name" attribute for standard and Carleton users.
     *
     * @return void
     */
    public function testFullNameAttribute()
    {
        $user = factory(User::class)->create();

        $this->assertFalse(ends_with($user->email, '@carleton.edu'));
        $this->assertTrue(ends_with($this->user->email, '@carleton.edu'));

        $this->assertEquals($user->name, $user->full_name);
        $this->assertEquals($this->user->name." '".substr($this->user->year, -2), $this->user->full_name);
    }

    /**
     * Test the "Priority - As Of" function. This determines what a user's
     * priority should have been *before* the specified term.
     *
     * @return void
     */
    public function testPriorityAsOf()
    {
        $term = factory(Term::class)->create([
            'on_air' => $this->term->on_air->subWeek(),
            'off_air' => $this->term->off_air->subWeek(),
        ]);
        $this->user->points()->create(['term_id' => $term->id, 'status' => 'issued']);
        $this->assertCount(1, $this->user->points->where('status', 'issued'));
        $this->assertEquals(0, $this->user->priorityAsOf($term->id)->terms);
        $this->assertEquals(1, $this->user->priorityAsOf($this->term->id)->terms);
        $this->assertEquals(0, $this->user->priorityAsOf('ASDF')->terms);
    }
}
