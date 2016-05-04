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

    public function player()
    {
        return $this->belongsTo(NflPlayer::class);
    }

    public function team()
    {
        return $this->belongsTo(NflTeam::class);
    }

    public static function fetchOrCreateStat($week, $type, $id)
    {
        $result = static::where('week', '=', $week)
            ->where($type.'_id', '=', $id)
            ->first();

        if (!$result) {
            $result = static::create([
                $type.'_id' => $id,
                'week' => $week,
            ]);
        }

        return $result;
    }
}
