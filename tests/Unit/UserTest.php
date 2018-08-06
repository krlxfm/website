<?php

namespace Tests\Unit;

use KRLX\Term;
use KRLX\User;
use Tests\TestCase;
use KRLX\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->states('contract_ok')->create();
    }

    /**
     * Test that users can request a password reset.
     *
     * @return void
     */
    public function testPasswordResetRequest()
    {
        Notification::fake();
        $this->withSession(['email', $this->user->email])->get('/password/reset');
        Notification::assertSentTo($this->user, ResetPassword::class);
    }

    /**
     * Test the "Full Name" attribute for standard and Carleton users.
     *
     * @return void
     */
    public function testFullNameAttribute()
    {
        $faker = $this->faker();
        $user = factory(User::class)->states('contract_ok')->create([
            'email' => $faker->username.'@carleton.edu',
            'year' => date('Y'),
        ]);

        $this->assertFalse(ends_with($this->user->email, '@carleton.edu'));
        $this->assertTrue(ends_with($user->email, '@carleton.edu'));

        $this->assertEquals($this->user->name, $this->user->full_name);
        $this->assertEquals($user->name." '".date('y'), $user->full_name);
    }

    /**
     * Test the "Priority - As Of" function. This determines what a user's
     * priority should have been before the specified term.
     *
     * @return void
     */
    public function testPriorityAsOf()
    {
        $term = factory(Term::class)->create();
        $this->assertCount(0, $this->user->points);
        $this->assertEquals(0, $this->user->priorityAsOf($term->id)->terms);
        $this->assertEquals(0, $this->user->priorityAsOf('ASDF')->terms);
    }
}
