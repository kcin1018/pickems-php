<?php

namespace Pickems\Transformers;

use League\Fractal\TransformerAbstract;

class PickTransformer extends TransformerAbstract
{
    public function transform(\stdClass $data)
    {
        return [
            'id' => 1,
            'week' => $data->week,
            'schedule' => json_encode($data->schedule),
            'pick1' => json_encode($data->pick1),
            'pick2' => json_encode($data->pick2),
            'picks-left' => json_encode($data->picks_left),
            'teams-picked' => json_encode($data->teams_picked),
        ];
    }
}
