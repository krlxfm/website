<?php

namespace Tests\Browser;

use KRLX\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test the email/password login flow for an existing account.
     *
     * @return void
     */
    public function testLogin()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->assertPresent('@login-tos')
                    ->assertMissing('@login-password')
                    ->type('email', $user->email)
                    ->check('@login-tos')
                    ->press('Continue');
        });
    }
}
