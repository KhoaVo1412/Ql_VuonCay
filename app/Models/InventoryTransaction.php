<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $table = 'inventory_transactions';

    protected $fillable = [
        'productID',
        'warehouseID',
        'unitID',
        'type',
        'quantity_changed',
        'balance_after',
        'reference_type',
        'reference_id',
        'code',
        'note',
        'created_by',
        'date'
    ];
    protected $appends = ['display_type'];
    protected $casts = [
        'date' => 'datetime',
    ];

    public function getDisplayTypeAttribute()
    {
        if ($this->type === 'export') return 'Xuất';
        if (
            $this->type === 'import'
            && $this->reference instanceof \App\Models\Picking
            && $this->reference->type === 'Khai thác'
        ) {
            return 'Khai thác';
        }
        return $this->type === 'import' ? 'Nhập' : 'Điều chỉnh';
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'productID');
    }
    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouseID');
    }
    public function unit()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unitID');
    }
    public function reference()
    {
        return $this->morphTo();
    }
}
