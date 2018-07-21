<?php

namespace Tests\Unit;

use KRLX\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     * Test the "Full Name" attribute for standard and Carleton users.
     *
     * @return void
     */
    public function testFullNameAttribute()
    {
        $faker = $this->faker();
        $user = factory(User::class)->create([
            'email' => $faker->username.'@carleton.edu',
            'year' => date('Y')
        ]);

        $this->assertFalse(ends_with($this->user->email, '@carleton.edu'));
        $this->assertTrue(ends_with($user->email, '@carleton.edu'));

        $this->assertEquals($this->user->name, $this->user->full_name);
        $this->assertEquals($user->name." '".date('y'), $user->full_name);
    }
}
