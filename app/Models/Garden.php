<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garden extends Model
{
    protected $table = 'gardens';

    protected $fillable = [
        'code',
        'gardenName',
        'gardenArea',
        'plotCount',
        'status',
    ];

    // public function plots()
    // {
    //     return $this->hasMany(Plot::class, 'id');
    // }
    public function plots()
    {
        return $this->hasMany(Plot::class, 'id', 'gardenID');
    }
}
