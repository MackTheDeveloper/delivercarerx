<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patients extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'patients';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'address_1',
        'address_2',
        'country',
        'state',
        'city',
        'zipcode',
        'phone_number',
        'facility_code',
        'gender',
        'dob',
        'patient_id',
        'patient_status',
        'newleaf_facility_id',
        'ipu',
        'shipping_method',
        'other_fields',
        'is_active',
        'newleaf_customer_id',
        'notes'
    ];
    protected $hidden = ['_token'];


    public function rxs()
    {
        return $this->hasMany(Rxs::class, 'customer_id', 'newleaf_customer_id');
    }

    public function refills()
    {
        return $this->hasMany(RefillOrder::class, 'newleaf_customer_id', 'newleaf_customer_id');
    }

    public function refillShipments()
    {
        return $this->hasManyThrough(
            RefillShipment::class,
            RefillOrder::class,
            'newleaf_customer_id',
            'refill_id',
            'newleaf_customer_id',
            'id'
        );
    }
}
