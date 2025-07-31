<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProposalPicking extends Model
{
    use HasFactory;

    protected $table = 'product_proposal_pickings';

    protected $fillable = [
        'propID',
        'pickingID',
        'quantity',
        'status',
    ];

    public function productProposal()
    {
        return $this->belongsTo(TaskProductProposalProduct::class);
    }

    public function picking()
    {
        return $this->belongsTo(Picking::class);
    }
}
