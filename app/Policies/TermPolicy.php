<?php

namespace KRLX\Policies;

use KRLX\Term;
use KRLX\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TermPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create shows.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\Term  $term
     * @return mixed
     */
    public function createShows(User $user, Term $term)
    {
        $active = $term->status == 'active';

        if ($active) {
            return true;
        } elseif ($term->status == 'pending' and $user->hasPermissionTo('override pending term')) {
            return true;
        } else {
            return $term->status == 'closed' and $user->hasPermissionTo('override closed term');
        }
    }
}
