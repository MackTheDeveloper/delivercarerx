<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartMaster extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cart_master';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'patient_id',
        'dob',
        'newleaf_customer_id',
        'flag_complete',
        'patient_name',
        'address_1',
        'address_2',
        'address_3',
        'zipcode',
        'shipping_method',
        'notes',
        'signature',
        'state_code',
        'city_code',
    ];
    protected $hidden = ['_token'];
}
