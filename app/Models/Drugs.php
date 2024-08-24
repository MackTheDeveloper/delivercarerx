<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drugs extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'drugs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'newleaf_drug_id',
        'created_by',
        'created_on',
        'updated_by',
        'updated_on',
        'identifier',
        'description',
        'strength',
        'new_ndc',
        'manufacturer_name',
        'is_generic',
        'is_rx',
        'status_code',
        'dosage_form_code',
        'direct_source',
        'master_description',
    ];
    protected $hidden = ['_token'];
}