<?php

namespace Pickems;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'paid', 'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function picks($week = null)
    {
        if ($week) {
            return $this->hasMany(TeamPick::class)
                ->where('week', '=', $week)
                ->orderBy('week', 'number');
        }

        return $this->hasMany(TeamPick::class)
            ->orderBy('week', 'number');
    }

    public function validatePicks($week = 18, $withTimeCheck = true)
    {
        // initialize the counts to what you start at
        $results = Config::get('pickems.counts');

        // for each pick from the user team
        foreach ($this->fetchPicks($week) as $pick) {
            if ($pick->stat_id === null) {
                continue;
            }

            if ($pick->stat->player_id !== null) {
                $pick->valid = 1;
                $pick->reason = null;

                switch ($pick->stat->player->position) {
                    case 'QB':
                    case 'K':
                        $key = strtolower($pick->stat->player->position);
                        break;
                    case 'RB':
                    case 'FB':
                        $key = 'rb';
                        break;
                    case 'WR':
                    case 'TE':
                        $key = 'wrte';
                        break;
                }

                /* check for position validity */
                if ($results[$key] <= 0) {
                    $pick->valid = 0;
                    $pick->reason = 'Already picked '.\Config::get('pickems.counts.'.$key).' '.strtoupper($key).'s ('.$results[$key].')';
                    $pick->save();
                    continue;
                }

                /* check for team validity */
                if (in_array($pick->stat->player->team->abbr, $results['teams'])) {
                    $pick->valid = 0;
                    $pick->reason = 'Already picked a player from '.$pick->stat->player->team->abbr;
                    $pick->save();
                    continue;
                }

                /* check for validity */
                if ($pick->playmaker === 1) {
                    if ($results['playmakers'] <= 0) {
                        $pick->valid = 0;
                        $pick->reason = 'Already used your '.\Config::get('pickems.counts.playmakers').' playmakers';
                        $pick->save();
                        continue;
                    }

                    --$results['playmakers'];
                }

                --$results[$key];
                $results['teams'][] = $pick->stat->player->team->abbr;
            }

            if ($pick->stat->team_id !== null) {
                $pick->valid = 1;
                $pick->reason = null;

                $key = strtolower($pick->stat->team->conference);
                /* check for conference validity */
                if ($results[$key] < 1) {
                    $pick->valid = 0;
                    $pick->reason = 'Already picked a '.$key.' team';
                    $pick->save();
                    continue;
                }

                --$results[$key];
            }

            if ($withTimeCheck === true && !\Auth::user()->admin) {
                // get the pick time
                $pickTime = strtotime($pick->updated_at);

                $game = $pick->game();
                if ($game) {
                    $gameTime = strtotime($game->start);

                    /* check for time validity */
                    if ($pickTime > $gameTime) {
                        $pick->valid = 0;
                        $pick->reason = 'Pick submitted too late';
                        $pick->save();
                        continue;
                    }
                }
            }

            $pick->save();
        }

        return $results;
    }

    public function fetchPicks($upToWeek = 18)
    {
        return $this->picks()
            ->with('stat', 'stat.player', 'stat.team', 'stat.player.team')
            ->where('week', '<', $upToWeek)
            ->orderBy('week', 'asc')
            ->get();
    }
}
