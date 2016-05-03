<?php

namespace Pickems\Http\Controllers\Api;

use Pickems\Team;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Pickems\Http\Controllers\Controller;
use Pickems\Transformers\PickTransformer;

class PicksController extends Controller
{
    use Helpers;

    public function show(Request $request, Team $team, $week = 1)
    {
        $data = new \stdClass();
        $data->week = $week;
        $data->schedule = [];

        $data->pick1 = [
            'selected' => null,
            'id' => null,
            'type' => null,
        ];

        $data->pick2 = [
            'selected' => null,
            'id' => null,
            'type' => null,
        ];

        $data->picks_left = [
            'QB' => 2,
            'RB' => 3,
            'WRTE' => 1,
            'K' => 6,
            'playmakers' => 1,
            'afc' => 0,
            'nfc' => 1,
        ];

        $data->teams_picked = [
            'AFC' => [
                ['abbr' => 'IND', 'used' => true],
            ],
            'NFC' => [
                ['abbr' => 'CHI', 'used' => false],
            ],
        ];

        return $this->response->item($data, new PickTransformer(), ['key' => 'picks']);
    }
}
