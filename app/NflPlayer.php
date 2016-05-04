<?php

namespace Pickems;

use Illuminate\Database\Eloquent\Model;

class NflPlayer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'gsis_id',
        'profile_id',
        'name',
        'position',
        'active',
    ];

    public function team()
    {
        return $this->belongsTo(NflTeam::class);
    }

    public function displayName()
    {
        return $this->name.'-'.$this->team->abbr.'-'.$this->position;
    }
}
