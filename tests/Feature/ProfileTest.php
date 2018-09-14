<?php

namespace Tests\Feature;

use KRLX\Term;
use KRLX\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public $term;
    public $user;

    public function setUp()
    {
        parent::setUp();

        $this->term = factory(Term::class)->create();
        $this->user = factory(User::class)->states('contract_ok')->create();
        $this->artisan('db:seed');
        $this->user->assignRole('board');
    }

    /**
     * Test that the profile view renders.
     *
     * @return void
     */
    public function testProfileViewRenders()
    {
        $request = $this->actingAs($this->user)->get('profile');

        $request->assertOk()
                ->assertViewIs('account.profile');
    }
}
