<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decompose extends Model
{
    use HasFactory;
    protected $table = 'decomposes';

    protected $fillable = [
        'name',
        'date',
        'warehouseID',
        'userID',
        'status',
        'active'
    ];

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function pickings()
    {
        return $this->hasMany(Picking::class);
    }
}
