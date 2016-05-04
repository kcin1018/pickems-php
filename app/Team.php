<?php

namespace Pickems;

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
}
