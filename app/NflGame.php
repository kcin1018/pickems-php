<?php

namespace Pickems;

use Illuminate\Database\Eloquent\Model;

class NflGame extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'week',
        'starts_at',
        'game_key',
        'game_id',
        'type',
        'home_team_id',
        'away_team_id',
        'winning_team_id',
        'losing_team_id',
    ];
}
