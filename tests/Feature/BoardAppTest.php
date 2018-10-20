<?php

namespace Tests\Feature;

use Tests\AuthenticatedTestCase;

class BoardAppTest extends AuthenticatedTestCase
{
    /**
     * Test that the base "Apply" view renders.
     *
     * @return void
     */
    public function testBaseApplyViewRenders()
    {
        $request = $this->actingAs($this->board)->get('/board/apply');
        $request->assertOk()
                ->assertSee(date('Y'));
    }
}
