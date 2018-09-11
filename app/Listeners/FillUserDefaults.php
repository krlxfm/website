<?php

namespace KRLX\Listeners;

use Jdenticon\Identicon;
use KRLX\Events\UserCreating;

class FillUserDefaults
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
     * @param  UserCreating  $event
     * @return void
     */
    public function handle(UserCreating $event)
    {
        $user = $event->user;
        $icon = new Identicon;
        $icon->setValue($user->email);
        $icon->setSize(300);

        $names = collect(explode(' ', $user->name))->filter(function ($name) {
            return strpos($name, '.') === false;
        });
        $user->first_name = $user->first_name ?? $names->first() ?? 'User';
        $user->title = $user->title ?? config('defaults.title', 'KRLX Community');
        $user->photo = $user->photo ?? $icon->getImageDataUri();
    }
}
