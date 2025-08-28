<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskProductProposalProduct extends Model
{
    use HasFactory;

    protected $table = 'task_product_proposal_products';
    protected $fillable = [
        'productID',
        'taskproposalID',
        'sessionID',
        'warehouseID',
        'unitID',
        'materialQuantity',
        'note',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID');
    }
    public function unit()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unitID');
    }

    public function taskProductProposal()
    {
        return $this->belongsTo(TaskProductProposal::class, 'taskproposalID');
    }

    public function productProposalPickings()
    {
        return $this->hasMany(ProductProposalPicking::class, 'propID', 'id');
    }
}
