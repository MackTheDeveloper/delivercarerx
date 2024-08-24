<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrescriberAddresses extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'prescriber_address';

    protected $fillable = [
        'prescriber_id',
        'prescriber_address_id',
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
