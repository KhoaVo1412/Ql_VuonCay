<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    protected $table = "contract_types";


    protected $fillable = [
        'contract_type_code',
        'contract_type_name',
        'contract_type_type'
    ];

    // public function contracts()
    // {
    //     return $this->hasMany(Contract::class);
    // }
    public function contracts()
    {
        return $this->belongsToMany(Contract::class, 'contract_type_customer')
            ->withTimestamps();
    }
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_customer_type');
    }
}
