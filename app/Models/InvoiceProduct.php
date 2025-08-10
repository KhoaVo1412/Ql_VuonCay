<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoiceID',
        'warehouseID',
        'unitID',
        'productID',
        'quantity',
        'slice',
        'quality',
        'price',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouseID');
    }

    public function unit()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unitID');
    }
}
