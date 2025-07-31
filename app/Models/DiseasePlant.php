<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseasePlant extends Model
{
    use HasFactory;
    protected $table = 'disease_plants';

    protected $fillable = [
        'detectionDate',
        'sessionID',
        'plantID',
        'specific_location',
        'diseaseID',
        'reportStatus',
        'tree_condition',
        'workerID',
        'infection_level',
        'treatmentResult',
        'active',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plantID', 'id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'workerID', 'id');
    }

    public function session()
    {
        return $this->belongsTo(TreatmentSessions::class, 'sessionID');
    }

    public function disease()
    {
        return $this->belongsTo(Diseases::class, 'diseaseID', 'id');
    }
}
