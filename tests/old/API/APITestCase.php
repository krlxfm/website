<?php

namespace Tests\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use KRLX\User;
use Tests\TestCase;

class APITestCase extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $session;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->states('contract_ok')->create();
        $this->session = $this->actingAs($this->user, 'api');
    }
}
