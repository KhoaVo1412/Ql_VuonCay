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
        'materialQuantity',
        'note',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function taskProductProposal()
    {
        return $this->belongsTo(TaskProductProposal::class);
    }

    public function productProposalPickings()
    {
        return $this->hasMany(ProductProposalPicking::class, 'propID', 'id');
    }
}
