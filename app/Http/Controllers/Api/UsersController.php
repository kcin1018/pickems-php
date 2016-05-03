<?php

namespace Pickems\Http\Controllers\Api;

use Pickems\User;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Hash;
use Pickems\Http\Controllers\Controller;
use Pickems\Transformers\UserTransformer;

class UsersController extends Controller
{
    use Helpers;

    public function index()
    {
        // fetch all the user data
        $users = User::all();

        return $this->response->collection($users, new UserTransformer(), ['key' => 'users']);
    }

    public function show(User $user)
    {
        return $this->response->item($user, new UserTransformer(), ['key' => 'users']);
    }

    public function store(Request $request)
    {
        // validate the incoming data
        $this->apiValidation($request, [
            'data.attributes.name' => 'required',
            'data.attributes.email' => 'required|email|unique:users,email',
            'data.attributes.password' => 'required',
        ]);

        // fetch all the data
        $data = $this->fetchData($request);

        // hash the password
        $data['password'] = Hash::make($data['password']);

        // create the user
        $user = User::create($data);

        // return the response with the user data
        return $this->response->item($user, new UserTransformer(), ['key' => 'users'])->setStatusCode(201);
    }

    public function update(Request $request, User $user)
    {
        // make sure the user is able to udpate data
        $this->apiAuthorize('edit', $user);

        // validate the incoming data
        $this->apiValidation($request, [
            'data.attributes.email' => 'email|unique:users,email,'.$user->id,
        ]);

        // fetch all the data
        $data = $this->fetchData($request);

        // hash password if present
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // update the values
        foreach ($data as $key => $value) {
            $user->$key = $value;
        }
        $user->save();

        return $this->response->item($user, new UserTransformer(), ['key' => 'users']);
    }

    public function destroy(User $user)
    {
        // make sure the user is able to udpate data
        $this->apiAuthorize('delete', $user);

        $user->delete();

        return $this->response->array([])->setStatusCode(204);
    }
}
