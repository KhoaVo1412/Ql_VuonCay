<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variety extends Model
{
    protected $table = 'varieties';

    protected $fillable = [
        'varietyName',
        'origin',
        'desc',
        'status',
    ];
    public function plants()
    {
        return $this->hasMany(Plant::class);
    }
}
