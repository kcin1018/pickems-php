<?php

namespace Pickems\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function edit(User $user, Team $team)
    {
        return $user->id === $team->user->id;
    }

    public function delete(User $user, User $team)
    {
        return $user->id === $team->user->id;
    }
}
