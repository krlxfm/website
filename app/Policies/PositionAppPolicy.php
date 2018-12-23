<?php

namespace KRLX\Policies;

use KRLX\User;
use KRLX\PositionApp;
use Illuminate\Auth\Access\HandlesAuthorization;

class PositionAppPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the position app.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\PositionApp  $boardApp
     * @return mixed
     */
    public function update(User $user, PositionApp $positionApp)
    {
        $app = $positionApp->board_app;

        return $user->id === $app->user_id and ! $app->submitted;
    }
}
