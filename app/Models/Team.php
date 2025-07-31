<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';

    protected $fillable = [
        'name',
        'teamName',
        'leader',
        'status',
    ];
    public function workers()
    {
        return $this->hasMany(Worker::class, 'team_id');
    }
}
