<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'company_name',
        'customer_type',
        'phone',
        'email',
        'address',
        'description',
        'user_id'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function contractTypes()
    {
        return $this->belongsToMany(ContractType::class, 'contract_type_customer')
            ->withTimestamps();
    }
    public function customerTypes()
    {
        return $this->belongsToMany(CustomerType::class, 'customer_customer_type');
    }
}
