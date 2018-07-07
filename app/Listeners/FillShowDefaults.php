<?php

namespace KRLX\Listeners;

use KRLX\Show;
use Jdenticon\Identicon;
use Faker\Factory as Faker;
use KRLX\Events\ShowCreating;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FillShowDefaults
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ShowCreating  $event
     * @return void
     */
    public function handle(ShowCreating $event)
    {
        $faker = Faker::create();
        $show = $event->show;

        /**
         * Generate a Show ID. To ensure that the ID is unique, we'll repeatedly
         * generate if there's a collision. Note that the probability of an ID
         * collision is 1/36 raised to the power of the ID length, which can be
         * adjusted in the config files. At 6 characters, the probability of a
         * collision on the first try is 1 in 2,176,782,336 -- better than 2^31.
         */
        do {
            $string = $faker->regexify('[A-Z0-9]{'.config('defaults.show_id_length', 6).'}');
            $check = Show::find($string);
            if(!$check) $show->id = $string;
        } while ($show->id == null);

        $show->content = [];
        $show->scheduling = [];
        $show->etc = [];
        $show->tags = [];
        $show->conflicts = [];
        $show->preferences = [];
        $show->classes = [];

        $show->title = $show->title ?? $show->track->name.' Show';

        $icon = new Identicon;
        $icon->setValue($show->id);
        $icon->setSize(300);
        $show->image = $icon->getImageDataUri();

        $show->special_times = array_fill_keys(array_keys(config('defaults.special_times')), 'm');
    }
}
