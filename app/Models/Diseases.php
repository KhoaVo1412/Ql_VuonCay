<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diseases extends Model
{
    use HasFactory;
    protected $table = 'diseases';

    protected $fillable = [
        'diseaseName',
        'desc',
        'status',
    ];
    public function infectedPlants()
    {
        return $this->hasMany(DiseasePlant::class);
    }
}
