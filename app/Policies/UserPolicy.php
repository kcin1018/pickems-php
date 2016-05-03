<?php

namespace Pickems\Policies;

use Pickems\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function edit(User $authUser, User $user)
    {
        return $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user)
    {
        return false;
    }
}
