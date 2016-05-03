<?php

namespace Pickems\Http\Controllers\Api;

use Pickems\User;
use Dingo\Api\Routing\Helpers;
use Pickems\Http\Controllers\Controller;
use Pickems\Transformers\UserTransformer;

class UsersController extends Controller
{
    use Helpers;

    public function list()
    {
        $users = User::all();

        return $this->response->collection($users, new UserTransformer(), 'users');
    }
}
