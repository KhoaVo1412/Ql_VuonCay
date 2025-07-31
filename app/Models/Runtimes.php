<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Runtimes extends Model
{
    use HasFactory;
    protected $table = 'runtimes';

    protected $fillable = ['runtime', 'updated_at'];
}
