<?php

namespace KRLX\Policies;

use KRLX\User;
use KRLX\Track;
use Illuminate\Auth\Access\HandlesAuthorization;

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
