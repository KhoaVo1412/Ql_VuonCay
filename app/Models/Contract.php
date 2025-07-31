<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;


    protected $table = 'contracts';

    protected $fillable = [
        'contract_code',
        'contract_type_id',
        'customer_id',
        'original_contract_number',
        'delivery_month',
        'quantity',
        'order_export_id',
        'contract_days',
        'product_type_name',
        'delivery_date',
        'packaging_type',
        'container_closing_date',
        'market',
        'production_or_trade_unit',
        'third_party_sale',
    ];


    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderExports()
    {
        return $this->hasMany(OrderExport::class);
    }

    // public function orderExport()
    // {
    //     return $this->belongsTo(OrderExport::class);
    // }
}
