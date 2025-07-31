<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'supplier',
        'status',
        'date',
    ];

    public function invoiceProducts()
    {
        return $this->hasMany(InvoiceProduct::class);
    }
    public function pickings()
    {
        return $this->hasMany(Picking::class);
    }
}
