<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cart';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_master_id',
        'rxnumber',
        'qty',
        'drug_id',
        'drug_name',
        'direction',
        'days',
    ];
    protected $hidden = ['_token'];
}

