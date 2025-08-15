<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialProposal extends Model
{
    use HasFactory;

    protected $table = 'material_proposals';

    protected $fillable = [
        'proposaName',
        'proposalDate',
        'approvalDate',
        'diseaseplantID',
        'status',
        'created_by',
        'reason',
    ];

    protected $casts = [
        'proposalDate' => 'date',
        'approvalDate' => 'date',
    ];

    public function diseasePlant()
    {
        return $this->belongsTo(DiseasePlant::class, 'diseaseplantID');
    }

    public function items()
    {
        return $this->hasMany(MaterialProposalItem::class, 'material_proposal_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
