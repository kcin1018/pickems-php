<?php

namespace Pickems\Http\Controllers\Api;

use Pickems\Team;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Pickems\Http\Controllers\Controller;
use Pickems\Transformers\TeamTransformer;

class TeamsController extends Controller
{
    use Helpers;

    public function index(Request $request)
    {
        // fetch logged in user
        $user = JWTAuth::toUser($request->headers->get('token'));

        // check if they want all teams
        if ($request->has('all_teams') && $user->admin) {
            return $this->response->collection(Team::all(), new TeamTransformer(), ['key' => 'teams']);
        }

        // fetch all the team data
        $teams = Team::where('user_id', '=', $user->id)
            ->get();

        return $this->response->collection($teams, new TeamTransformer(), ['key' => 'teams']);
    }

    public function show(Team $team)
    {
        return $this->response->item($team, new TeamTransformer(), ['key' => 'teams']);
    }

    public function store(Request $request)
    {
        // validate the incoming data
        $this->apiValidation($request, [
            'data.attributes.name' => 'required|unique:teams,name',
            'data.attributes.paid' => 'required|boolean',
            'data.relationships.user.data.id' => 'required|integer',
        ]);

        // fetch all the data
        $data = $this->fetchData($request);

        // create the team
        $team = Team::create($data);

        // return the response with the team data
        return $this->response->item($team, new TeamTransformer(), ['key' => 'teams'])->setStatusCode(201);
    }

    public function update(Request $request, Team $team)
    {
        // make sure the team is able to udpate data
        $this->apiAuthorize('edit', $team);

        // validate the incoming data
        $this->apiValidation($request, [
            'data.attributes.name' => 'required|unique:teams,name,'.$team->id,
            'data.attributes.paid' => 'required|boolean',
            'data.relationships.user.data.id' => 'required|integer',
        ]);

        // fetch all the data
        $data = $this->fetchData($request);

        // update the values
        foreach ($data as $key => $value) {
            $team->$key = $value;
        }
        $team->save();

        return $this->response->item($team, new TeamTransformer(), ['key' => 'teams']);
    }

    public function destroy(Team $team)
    {
        // make sure the team is able to udpate data
        $this->apiAuthorize('delete', $team);

        $team->delete();

        return $this->response->array([])->setStatusCode(204);
    }
}
