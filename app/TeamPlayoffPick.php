<?php

namespace Pickems;

use Illuminate\Database\Eloquent\Model;

class TeamPlayoffPick extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'starting_points',
        'picks',
        'valid',
        'reason',
    ];
}
