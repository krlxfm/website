<?php

namespace Tests\Unit;

use KRLX\Config;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfigTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Config::valueOr - if the value for the specified key exists, use it,
     * otherwise use the provided default.
     *
     * @return void
     */
    public function testExample()
    {
        Config::create(['name' => 'field exists', 'value' => '5']);

        $this->assertEquals('5', Config::valueOr('field exists', 'potato'));
        $this->assertEquals('potato', Config::valueOr('field does not exist', 'potato'));
    }
}
