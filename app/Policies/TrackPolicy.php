<?php

namespace KRLX\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use KRLX\Track;
use KRLX\User;

class TrackPolicy
{
    use HandlesAuthorization;

    /**
     * Authorize track creation.
     *
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('manage tracks');
    }
}
