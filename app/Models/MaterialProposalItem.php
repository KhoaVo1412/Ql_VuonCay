<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialProposalItem extends Model
{
    use HasFactory;

    protected $table = 'material_proposal_items';

    protected $fillable = [
        'material_proposal_id',
        'warehouseID',
        'productID',
        'materialQuantity',
        'unitID',
        'note',
        'status',
    ];

    public function proposal()
    {
        return $this->belongsTo(MaterialProposal::class, 'material_proposal_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouseID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID'); // hoặc Material::class
    }

    public function unit()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unitID');
    }
}
