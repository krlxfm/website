<?php

namespace KRLX\Policies;

use KRLX\Show;
use KRLX\Term;
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
        return $show->hosts->contains($user) or $user->can('see all applications');
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
        $host = ($show->hosts->contains($user) or $user->can('edit all applications'));

        $term = $show->term->status == 'active';
        if (! $term and $user->hasAnyPermission(['override pending term', 'override closed term'])) {
            $pending = ($show->term->status == 'pending' and $user->can('override pending term'));
            $closed = ($show->term->status == 'closed' and $user->can('override closed term'));
            $term = ($pending or $closed);
        }

        return $host and $term;
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
        return $this->update($user, $show);
    }
}
