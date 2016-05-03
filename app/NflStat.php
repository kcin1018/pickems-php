<?php

namespace Pickems;

use Illuminate\Database\Eloquent\Model;

class NflStat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'player_id',
        'team_id',
        'week',
        'td',
        'fg',
        'xp',
        'two',
        'diff',
    ];
}
