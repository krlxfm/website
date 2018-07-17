<?php

namespace KRLX\Policies;

use KRLX\Show;
use KRLX\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the show.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\Show  $show
     * @return mixed
     */
    public function view(User $user, Show $show)
    {
        //
    }

    /**
     * Determine whether the user can create shows.
     *
     * @param  \KRLX\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the show.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\Show  $show
     * @return mixed
     */
    public function update(User $user, Show $show)
    {
        return $show->hosts->contains($user);
    }

    /**
     * Determine whether the user can delete the show.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\Show  $show
     * @return mixed
     */
    public function delete(User $user, Show $show)
    {
        return $show->hosts->contains($user);
    }
}
