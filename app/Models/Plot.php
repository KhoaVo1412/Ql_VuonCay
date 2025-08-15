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
        'status',
        'fid',
        'idmap',
        'hien_trang',
        'layer',
        'quoc_gia',
        'chi_tieu',
        'dien_tich',
        'tapping_y',
        'repl_time',
        'find',
        'webmap',
        'gwf',
        'xa',
        'huyen',
        'nguon_goc_lo',
        'nguon_goc_dat',
        'hang_dat',
        'x',
        'y',
        'chu_thich',
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
