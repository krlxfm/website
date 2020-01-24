<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use KRLX\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

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
        $user = factory(User::class)->create();
        $request = $this->withSession(['email', $user->email])->get('/password/reset');
        $request->assertRedirect('/login');
    }
}
