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
    ];
    public function diseasePlants()
    {
        return $this->hasMany(DiseasePlant::class, 'sessionID');
    }
    public function taskProductProposals()
    {
        return $this->hasMany(TaskProductProposal::class, 'id', 'sessionID');
    }
}
