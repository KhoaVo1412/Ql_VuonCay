<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPicking extends Model
{
    use HasFactory;

    protected $table = 'product_pickings';

    protected $fillable = [
        'productID',
        'pickingID',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID');
    }

    public function picking()
    {
        return $this->belongsTo(Picking::class, 'pickingID');
    }
}
