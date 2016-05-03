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
}
