<?php

namespace Pickems\Transformers;

use Pickems\Team;
use League\Fractal\TransformerAbstract;

class TeamTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'user',
    ];

    public function transform(Team $team)
    {
        return [
            'id' => (int) $team->id,
            'name' => $team->name,
            'paid' => (bool) $team->paid,
        ];
    }

    public function includeUser(Team $team)
    {
        return $this->item($team->user, new UserTransformer(), 'users');
    }
}
