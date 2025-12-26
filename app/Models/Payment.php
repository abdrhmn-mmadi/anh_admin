<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'bank_id',
        'bonus',
        'total_amount',
        'payment_type',
        'payment_date',
        'month',
        'reference',
    ];

    protected $casts = [
        'payment_date' => 'datetime', // <-- add this line
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
