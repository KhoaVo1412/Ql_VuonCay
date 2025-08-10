<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentSessions extends Model
{
    use HasFactory;
    protected $table = 'treatment_sessions';

    protected $fillable = [
        'sessionStart',
        'sessionEnd',
        'desc',
        'status',
        'assigned_to',
        'priority',
        'diseasePlantID'
    ];
    // public function diseasePlants()
    // {
    //     return $this->hasMany(DiseasePlant::class, 'sessionID');
    // }
    public function diseasePlant()
    {
        return $this->belongsTo(DiseasePlant::class, 'diseasePlantID');
    }
    public function taskProductProposals()
    {
        return $this->hasMany(TaskProductProposal::class, 'id', 'sessionID');
    }
    public function treatmentSteps()
    {
        return $this->hasMany(TreatmentStep::class, 'sessionID');
    }
    public function assignedWorker()
    {
        return $this->belongsTo(Worker::class, 'assigned_to');
    }
    public function treatmentSessions()
    {
        return $this->hasMany(TreatmentSessions::class, 'diseasePlantID');
    }
}
