<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Refill extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'refills';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'refill_id','refill_number','created_on', 'created_by','updated_on','updated_by',
        'rx_id','drug_id','destination_type_id','destination_date','customer_address_id',
        'status','facility_address_id','package_choice','date_filled','sig','sig_expanded',
        'destination_notes','dispensed_quantity','days_supply','min_days_supply','max_days_supply',
        'number_of_pieces','rph_user_name','rph_user_id','is_ordered','is_dispensed','is_prefill',
        'discard_after_date','workflow_status','number_of_labels','doses_per_day','units_per_dose',
        'destination_address1','destination_address2','destination_city','destination_state',
        'destination_zip','effective_date','prescriber_address_id'
    ];
    protected $guarded = [];
    protected $hidden = ['_token'];

}
