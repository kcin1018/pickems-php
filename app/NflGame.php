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

    public static function fetchSchedule($week)
    {
        return self::where('week', '=', $week)
            ->orderBy('starts_at')
            ->get();
    }

    public static function fetchCurrentWeek()
    {
        // get the first game before the current date
        $game = static::where('starts_at', '>=', date('Y-m-d H:i:s'))
            ->orderBy('starts_at', 'asc')
            ->first();

        // return the week of that game (none return then last week)
        return (isset($game->week)) ? $game->week : 23;
    }

    public static function fetchPlayoffTeams()
    {
        $teams = [];
        foreach (static::whereIn('week', [18, 19])->get() as $game) {
            $teams[$game->away_team_id] = $game->away_team_id;
            $teams[$game->home_team_id] = $game->home_team_id;
        }

        return $teams;
    }
}
