<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDecompose extends Model
{
    use HasFactory;
    protected $table = 'product_decomposes';

    protected $fillable = [
        'productID',
        'decomposeID',
        'productDecomposeID',
        'unitDecomposeID',
        'productUnitID',
        'quantityDecompose',
        'quantityProduct',
    ];
    public function oldProduct()
    {
        return $this->belongsTo(Product::class, 'productID');
    }

    public function newProduct()
    {
        return $this->belongsTo(Product::class, 'productDecomposeID');
    }

    public function decompose()
    {
        return $this->belongsTo(Decompose::class, 'decomposeID');
    }

    public function unitDecompose()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unitDecomposeID');
    }
    public function productUnit()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'productUnitID');
    }
}
