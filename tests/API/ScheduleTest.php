<?php

namespace Tests\API;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use KRLX\Jobs\FinalPublishShow;
use KRLX\Jobs\PublishShow;
use KRLX\Show;
use Tests\AuthenticatedTestCase;

class ScheduleTest extends AuthenticatedTestCase
{
    use WithFaker;

    public $show;

    public function setUp()
    {
        parent::setUp();
        Queue::fake();

        $this->show = factory(Show::class)->create(['submitted' => true, 'term_id' => $this->term->id]);
    }

    /**
     * Test an update to a show's scheduling data.
     *
     * @return void
     */
    public function testScheduleUpdateForShow()
    {
        $request = $this->actingAs($this->board, 'api')->json('PATCH', "/api/v1/schedule/{$this->show->id}", [
            'day' => 'Sunday',
            'start' => '12:00',
            'end' => '13:00',
        ]);

        $request->assertOk();
        $show_updated = Show::find($this->show->id);

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
        $faker = $this->faker();

        $shows = factory(Show::class, 10)->create([
            'term_id' => $this->term->id,
            'submitted' => true,
            'start' => $faker->time('H:i'),
            'end' => $faker->time('H:i'),
            'day' => $faker->date('l'),
        ]);

        $publish_array = ['publish' => $shows->pluck('id')->all()];
        $request = $this->actingAs($this->board, 'api')->json('PATCH', '/api/v1/schedule/publish', $publish_array);
        $request->assertStatus(202);

        Queue::assertPushed(PublishShow::class, 10);
        foreach ($shows as $show) {
            Queue::assertPushed(PublishShow::class, function ($job) use ($show) {
                return $job->show->id === $show->id;
            });
        }
    }

    /**
     * Test that when we call the "publish" route for FINAL publication, that
     * FINAL PUBLICATION jobs are dispatched to the queue for processing.
     *
     * @return void
     */
    public function testFinalPublishingDispatchesJobs()
    {
        $faker = $this->faker();

        $shows = factory(Show::class, 10)->create([
            'term_id' => $this->term->id,
            'submitted' => true,
            'start' => $faker->time('H:i'),
            'end' => $faker->time('H:i'),
            'day' => $faker->date('l'),
        ]);

        $publish_array = ['publish' => [$shows->first()->id], 'final' => $this->term->id];
        $request = $this->actingAs($this->board, 'api')->json('PATCH', '/api/v1/schedule/publish', $publish_array);
        $request->assertStatus(202);

        Queue::assertPushed(FinalPublishShow::class, 10);
        Queue::assertNotPushed(PublishShow::class);
        foreach ($shows as $show) {
            Queue::assertPushed(FinalPublishShow::class, function ($job) use ($show) {
                return $job->show->id === $show->id;
            });
        }
    }

    /**
     * Test the responses from the publication progress monitor.
     *
     * @return void
     */
    public function testSchedulePublishMonitorProgress()
    {
        $faker = $this->faker();

        $show = factory(Show::class)->create([
            'term_id' => $this->term->id,
            'submitted' => true,
            'start' => $faker->time('H:i'),
            'end' => $faker->time('H:i'),
            'day' => $faker->date('l'),
        ]);

        $request = $this->actingAs($this->board, 'api')->json('GET', '/api/v1/schedule/publish');
        $request->assertStatus(204);

        $publish_array = ['publish' => [$show->id]];
        $request = $this->actingAs($this->board, 'api')->json('PATCH', '/api/v1/schedule/publish', $publish_array);
        $request->assertStatus(202);

        $contents = file_get_contents(storage_path('app/publish'));
        file_put_contents(storage_path('app/publish'), json_encode(['position' => 1, 'show' => $show->id]));

        $request = $this->actingAs($this->board, 'api')->json('GET', '/api/v1/schedule/publish');
        file_put_contents(storage_path('app/publish'), $contents);
        $request->assertStatus(200)
                ->assertJsonFragment(['show' => $show->id]);
    }
}
