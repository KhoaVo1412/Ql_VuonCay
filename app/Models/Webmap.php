<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webmap extends Model
{
    use HasFactory;
    protected $table = 'webmap';

    protected $fillable = [
        'id',
        'webapp',
        'webmap'
    ];
}
