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

    public function displayName()
    {
        return $this->city.' '.$this->name.'-'.$this->abbr.'-'.$this->conference;
    }
}
