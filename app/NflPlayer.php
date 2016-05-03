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
}
