<?php

namespace KRLX\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use KRLX\BoardApp;
use KRLX\User;

class BoardAppPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the board app.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\BoardApp  $boardApp
     * @return mixed
     */
    public function view(User $user, BoardApp $boardApp)
    {
        return $user->id === $boardApp->user_id or $user->can('review board applications');
    }

    /**
     * Determine whether the user can create board apps.
     *
     * @param  \KRLX\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('apply for board seats');
    }

    /**
     * Determine whether the user can update the board app.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\BoardApp  $boardApp
     * @return mixed
     */
    public function update(User $user, BoardApp $boardApp)
    {
        return $user->id === $boardApp->user_id;
    }

    /**
     * Determine whether the user can delete the board app.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\BoardApp  $boardApp
     * @return mixed
     */
    public function delete(User $user, BoardApp $boardApp)
    {
        return $user->id === $boardApp->user_id;
    }

    /**
     * Determine whether the user can restore the board app.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\BoardApp  $boardApp
     * @return mixed
     */
    public function restore(User $user, BoardApp $boardApp)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the board app.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\BoardApp  $boardApp
     * @return mixed
     */
    public function forceDelete(User $user, BoardApp $boardApp)
    {
        //
    }
}
