<?php

namespace Pickems;

use Illuminate\Database\Eloquent\Model;

class NflTeam extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'abbr', 'conference', 'city', 'name',
    ];
}
