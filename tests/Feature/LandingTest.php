<?php

namespace Tests\Feature;

use Tests\AuthenticatedTestCase;

class LandingTest extends AuthenticatedTestCase
{
    /**
     * Test that the landing page loads okay.
     *
     * @return void
     */
    public function testLandingPageLoads()
    {
        $request = $this->get('/');
        $request->assertOk();
    }
}
