<?php

namespace Tests\Feature;

use KRLX\User;
use Tests\AuthenticatedTestCase;

class OnboardTest extends AuthenticatedTestCase
{
    /**
     * Test that onboarding is mandatory for Carleton accounts.
     *
     * @return void
     */
    public function testCarletonOnboardingMandatory()
    {
        $user = factory(User::class)->states('carleton_new')->create();

        $this->assertNull($user->phone_number);

        $request = $this->actingAs($user)->get('/home');
        $request->assertRedirect('/welcome');
    }

    /**
     * Test that non-Carleton accounts get redirected away from the Carleton
     * onboarding processs.
     *
     * @return void
     */
    public function testNonCarletonAccountsGoHome()
    {
        $request = $this->actingAs($this->guest)->get('/welcome');
        $request->assertRedirect('/home');
    }
}
