<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductType;
use App\Models\User;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /* =====================
       RELATIONSHIPS
    ====================== */

    public function product()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
