<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractContractType extends Model
{
    protected $table = 'contract_contract_type';

    protected $fillable = [
        'contract_id',
        'contract_type_id',
        // Thêm các cột khác nếu có như: 'is_main', 'note'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }
}
