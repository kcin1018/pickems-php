<?php

namespace Pickems\Http\Controllers\Api;

use Pickems\Team;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Pickems\Http\Controllers\Controller;
use Pickems\Transformers\TeamTransformer;

class TeamsController extends Controller
{
    use Helpers;

    public function index()
    {
        // fetch all the team data
        $teams = Team::all();

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
