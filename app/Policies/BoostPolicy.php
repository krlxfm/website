<?php

namespace KRLX\Policies;

use KRLX\User;
use KRLX\Boost;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can redeem the upgrade certificate.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\Show  $show
     * @return mixed
     */
    public function redeem(User $user, Boost $boost)
    {
        return $boost->user_id == $user->id;
    }
}
