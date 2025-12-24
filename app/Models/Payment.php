<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'employee_id',
        'bank_id',
        'bonus',
        'total_amount',
        'payment_type',
        'payment_date',
        'month',
        'reference'
    ];


    /* =====================
       Relationships
    ====================== */

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
