<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostLogin extends Model

{
    use HasFactory;
    protected $table = 'post _login';
    protected $fillable = [
        'name',
        'desc',
        'company_name',
        'commune_name',
        'link',
        'support'
    ];
}
