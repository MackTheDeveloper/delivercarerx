<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefillAdjudcation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'refill_adjudications';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'refill_adjudication_id','updated_on','updated_by',
        'refill_plan_order','third_party_id','adjudication_type','print_copies','print_monograph',
        'workstation_name','refill_adjudication_status','refill_id','customer_id','claim_data','customer_ar_account_id','reset_aging'
    ];
    protected $guarded = [];
    protected $hidden = ['_token'];
}
