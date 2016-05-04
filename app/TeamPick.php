<?php

namespace Pickems;

use Illuminate\Database\Eloquent\Model;

class TeamPick extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'week',
        'number',
        'stat_id',
        'playmaker',
        'valid',
        'reason',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function stat()
    {
        return $this->belongsTo(NflStat::class);
    }
}
