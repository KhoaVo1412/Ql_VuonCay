<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WareHouse extends Model
{
    use HasFactory;
    protected $table = 'ware_houses';

    protected $fillable = [
        'name',
        'code',
        'status',
    ];
    public function decomposes()
    {
        return $this->hasMany(Decompose::class);
    }
    public function pickings()
    {
        return $this->hasMany(Picking::class);
    }
    public function categories()
    {
        return $this->hasMany(Category::class, 'warehouseID');
    }
}
