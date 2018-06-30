<?php

namespace Tests\Unit;

use KRLX\Term;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TermTest extends TestCase
{
    use RefreshDatabase;

    public $term;

    public function setUp()
    {
        parent::setUp();
        $this->term = factory(Term::class)->create();
    }

    /**
     * Test the "name" attribute for terms that have random names (like from
     * factories).
     *
     * @return void
     */
    public function testRandomTermNamesDoNotTranslate()
    {
        $components = explode('-', $this->term->id);
        $this->assertEquals(str_replace('_', ' ', title_case($components[1])).' '.date('Y'), $this->term->name);
    }
}
