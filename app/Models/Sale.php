<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'invoice_type',
        'grand_total',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
