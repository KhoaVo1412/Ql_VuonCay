<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decompose extends Model
{
    use HasFactory;
    protected $table = 'decomposes';

    protected $fillable = [
        'code',
        'name',
        'date',
        'warehouseID',
        'userID',
        'status',
        'desc',
        'active'
    ];

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouseID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
    public function pickings()
    {
        return $this->hasMany(Picking::class);
    }
    public function items()
    {
        return $this->hasMany(ProductDecompose::class, 'decomposeID');
    }
}
