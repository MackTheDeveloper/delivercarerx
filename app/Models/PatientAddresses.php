<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientAddresses extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'patient_addresses';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'newleaf_customer_id',
        'newleaf_customer_address_id',
        'address_type',
        'address_1',
        'address_2',
        'country',
        'state',
        'city',
        'zipcode',
        'is_primary',
        'comment',
        'other_fields',
        'is_active',
        'created_by',
        'updated_by'
    ];
    protected $hidden = ['_token'];
}
