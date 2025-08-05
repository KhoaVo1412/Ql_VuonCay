<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    protected $table = 'inventory_stocks';

    protected $fillable = [
        'productID',
        'warehouseID',
        'quantity',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID');
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouseID');
    }
}
