<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskProductProposal extends Model
{
    protected $table = 'task_product_proposals';
    protected $fillable = [
        'proposaName',
        'proposalDate',
        'approvalDate',
        'is_exported',
        'taskID',
        'treatmentID',
        'sessionID',
        'request_status',
        'created_by',
        'reason',
        'status',
    ];
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function task()
    {
        return $this->belongsTo(GenTask::class, 'taskID', 'id');
    }

    // public function treatment()
    // {
    //     return $this->belongsTo(Treatment::class, 'treamentID', 'id');
    // }

    public function session()
    {
        return $this->belongsTo(TreatmentSessions::class, 'sessionID', 'sessionID');
    }
    // public function proposalProducts()
    // {
    //     return $this->hasMany(TaskProductProposalProduct::class, 'TaskpropID', 'TaskpropID');
    // }
    public function proposalProducts()
    {
        return $this->hasMany(TaskProductProposalProduct::class, 'taskproposalID', 'id');
    }
}
