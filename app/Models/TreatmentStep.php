<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'sessionID',
        'productID',
        'dose',
        'execution_date',
        'instructions'
    ];


    public function treatmentSession()
    {
        return $this->belongsTo(TreatmentSessions::class, 'sessionID');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'sessionID');
    }
}
