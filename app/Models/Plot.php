<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Garden;

class Plot extends Model
{
    use HasFactory;
    protected $table = 'plots';

    protected $fillable = [
        'plotCode',
        'plotName',
        'plotArea',
        'gardenID',
        'plantCount',
        'mapJs',
        'year',
        'statusTree',
        'status'
    ];

    // public function garden()
    // {
    //     return $this->belongsTo(Garden::class);
    // }

    public function genTasks()
    {
        return $this->hasMany(GenTask::class);
    }
    public function plants()
    {
        return $this->hasMany(Plant::class, 'plotID');
    }
    public function garden()
    {
        return $this->belongsTo(Garden::class, 'gardenID', 'id');
    }
}
