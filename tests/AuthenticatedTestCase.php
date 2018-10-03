<?php

namespace Tests;

use KRLX\Term;
use KRLX\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticatedTestCase extends TestCase
{
    use RefreshDatabase;

    public $guest;
    public $carleton;
    public $board;
    public $term;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->term = factory(Term::class)->states('active')->create();
        $this->guest = factory(User::class)->create();
        $this->carleton = factory(User::class)->states('carleton', 'contract_ok')->create();
        $this->board = factory(User::class)->states('carleton', 'contract_ok', 'board')->create();
    }
}
