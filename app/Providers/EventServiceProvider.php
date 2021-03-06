<?php

namespace KRLX\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'KRLX\Events\BoardAppCreating' => [
            'KRLX\Listeners\FillBoardAppDefaults',
        ],
        'KRLX\Events\PositionAppCreating' => [
            'KRLX\Listeners\FillPositionAppDefaults',
        ],
        'KRLX\Events\ShowCreating' => [
            'KRLX\Listeners\FillShowDefaults',
        ],
        'KRLX\Events\TrackCreating' => [
            'KRLX\Listeners\FillTrackDefaults',
        ],
        'KRLX\Events\TermCreating' => [
            'KRLX\Listeners\FillTermDefaults',
        ],
        'KRLX\Events\UserCreating' => [
            'KRLX\Listeners\FillUserDefaults',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
