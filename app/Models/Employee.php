<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'sex',
        'dob',
        'address',
        'email',
        'phone',
        'salary',
        'account_number',
        'nin',
        'bank_id',
        'region_id',
        'department_id',
        'service_id',
        'position',
        'contract_type',
        'date_recruited',
        // ❌ DO NOT add matricule here (auto-generated)
    ];

    /**
     * Boot method to auto-generate matricule
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {

            DB::transaction(function () use ($employee) {

                $lastMatricule = self::lockForUpdate()
                    ->orderBy('id', 'desc')
                    ->value('matricule');

                if ($lastMatricule) {
                    // Get last 3 digits
                    $lastNumber = (int) substr($lastMatricule, -3);
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                $employee->matricule = 'ANH-KM-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            });
        });
    }

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
