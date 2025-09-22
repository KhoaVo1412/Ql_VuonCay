<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Picking extends Model
{
    use HasFactory;

    protected $table = 'pickings';

    protected $fillable = [
        'code',
        'name',
        'type',
        'warehouseID',
        'createDate',
        'createName',
        'active',
        'status',
        'decomposeID',
        'invoiceID',
        'taskID',
        'desc',
    ];

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouseID', 'id');
    }
    public function decompose()
    {
        return $this->belongsTo(Decompose::class, 'decomposeID');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceID');
    }

    public function task()
    {
        return $this->belongsTo(GenTask::class, 'taskID', 'id');
    }

    public function productPickings()
    {
        return $this->hasMany(ProductPicking::class, 'pickingID', 'id');
    }
    public function productProposalPickings()
    {
        return $this->hasMany(ProductProposalPicking::class, 'pickingID', 'id');
    }
}
