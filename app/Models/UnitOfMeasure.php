<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOfMeasure extends Model
{
    use HasFactory;
    protected $table = 'unit_of_measures';

    protected $fillable = [
        'code',
        'name',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'unitID');
    }
}
