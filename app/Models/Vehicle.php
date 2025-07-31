<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $table = 'vehicles';
    protected $fillable = ['unit_id', 'driver_name', 'vehicle_name', 'vehicle_number', 'vehicle_type', 'status'];

    // public function farm()
    // {
    //     return $this->belongsTo(Farm::class);
    // }
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'vehicle_number_id');
    }
    public function unit()
    {
        return $this->belongsTo(Units::class, 'unit_id');
    }
}
