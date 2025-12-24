<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'address',
        'email',
        'phone',
        'salary',
        'account_number',
        'nin', // Added NIN
        'bank_id',
        'region_id',
        'department_id',
        'service_id',
        'position',
        'contract_type',
        'date_recruited',
    ];

    /* =====================
       Relationships
    ====================== */

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
