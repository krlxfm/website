<?php

namespace Tests\API;

use KRLX\Show;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleTest extends APITestCase
{
    use RefreshDatabase;

    /**
     * Test an update to a show's scheduling data.
     *
     * @return void
     */
    public function testScheduleUpdateForShow()
    {
        $show = factory(Show::class)->create();

        $request = $this->json('PATCH', "/api/v1/schedule/{$show->id}", [
            'day' => 'Sunday',
            'start' => '12:00',
            'end' => '13:00'
        ]);

        $request->assertOk();
        $show_updated = Show::find($show->id);

        $this->assertEquals('Sunday', $show_updated->day);
        $this->assertEquals('12:00', $show_updated->start);
        $this->assertEquals('13:00', $show_updated->end);
    }
}
