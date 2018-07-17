<?php

namespace Tests\API;

use KRLX\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APITestCase extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->session = $this->actingAs($this->user, 'api');
    }
}
