<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'desc',
        'supplier',
        'status',
        'date',
    ];

    public function invoiceProducts()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoiceID');
    }
    public function pickings()
    {
        return $this->hasMany(Picking::class);
    }
}
