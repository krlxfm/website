<?php

namespace Tests\API;

use KRLX\Term;
use KRLX\Show;
use KRLX\Jobs\PublishShow;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleTest extends APITestCase
{
    use RefreshDatabase, WithFaker;

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
            'end' => '13:00',
        ]);

        $request->assertOk();
        $show_updated = Show::find($show->id);

        $this->assertEquals('Sunday', $show_updated->day);
        $this->assertEquals('12:00', $show_updated->start);
        $this->assertEquals('13:00', $show_updated->end);
    }

    /**
     * Test that when we call the "publish" route, that jobs are dispatched to
     * the queue for processing.
     *
     * @return void
     */
    public function testPublishingDispatchesJobs()
    {
        Queue::fake();
        $faker = $this->faker();

        $term = factory(Term::class)->create(['accepting_applications' => true]);
        $shows = factory(Show::class, 10)->create([
            'term_id' => $term->id,
            'submitted' => true,
            'start' => $faker->time('H:i'),
            'end' => $faker->time('H:i'),
            'day' => $faker->date('l'),
        ]);

        $publish_array = ['publish' => $shows->pluck('id')->all()];
        $request = $this->json('PATCH', '/api/v1/schedule/publish', $publish_array);
        $request->assertStatus(202);

        Queue::assertPushed(PublishShow::class, 10);
        foreach($shows as $show) {
            Queue::assertPushed(PublishShow::class, function ($job) use ($show) {
                return $job->show->id === $show->id;
            });
        }
    }
}
