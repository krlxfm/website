<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use KRLX\Term;
use KRLX\User;

class UnitBaseCase extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $term;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->term = factory(Term::class)->states('active')->create();
        $this->user = factory(User::class)->states('carleton', 'contract_ok', 'board')->create();
    }
}
