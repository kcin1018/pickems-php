<?php

namespace Pickems\Http\Controllers\Api;

use Pickems\Team;
use Pickems\User;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Config;
use Pickems\Http\Controllers\Controller;

class GeneralController extends Controller
{
    use Helpers;

    public function homeStats()
    {
        $teamsPaid = Team::where('paid', '=', true)->count();

        return $this->response->array([
            'teams' => Team::count(),
            'paid' => $teamsPaid,
            'owners' => User::count() - 1,
            'money' => $teamsPaid * Config::get('pickems.cost'),
        ]);
    }
}
