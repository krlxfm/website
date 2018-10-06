<?php

namespace Tests\Unit;

use KRLX\Term;
use Tests\UnitBaseCase;

class TermTest extends UnitBaseCase
{
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

    /**
     * Test the "name" attribute for a standard term (like 2018-SP) actually
     * matches up with the config value.
     *
     * @return void
     */
    public function testStandardTermNamesFollowConfig()
    {
        $term = factory(Term::class)->create(['id' => date('Y').'-FA']);
        $this->assertEquals(config('terms.FA', 'Fa').' '.date('Y'), $term->name);
    }
}
