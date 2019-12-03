<?php

namespace KRLX\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use KRLX\Show;
use KRLX\User;

class ShowPolicy
{
    use HandlesAuthorization;

    /**
     * For API transactions, guests are allowed to receive some limited
     * information about a show, such as its description and host names. These
     * details are encapsulated in a ShowResource rather than a full Show.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\Show  $show
     * @return bool
     */
    public function basicView(User $user, Show $show)
    {
        return true;
    }

    /**
     * Determine whether the user can view the show.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\Show  $show
     * @return bool
     */
    public function view(User $user, Show $show)
    {
        return $show->hosts->contains($user) or $user->can('see all applications');
    }

    /**
     * Determine whether the user can create shows at all.
     *
     * @param  \KRLX\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return ends_with($user->email, '@carleton.edu');
    }

    /**
     * Determine whether the user can update the show.
     *
     * @param  \KRLX\User  $user
     * @param  \KRLX\Show  $show
     * @return bool
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
     * @return bool
     */
    public function delete(User $user, Show $show)
    {
        return $this->update($user, $show);
    }
}
