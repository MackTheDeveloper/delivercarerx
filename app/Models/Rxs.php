<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rxs extends Model
{
    use HasFactory;
    protected $table = 'rxs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rx_id', 'rx_number', 'customer_id', 'created_on', 'created_by', 'updated_on', 'updated_by', 'status', 'origin', 'daw_code',
        'prescriber_id', 'prescribed_drug_id', 'original_quantity', 'owed_quantity', 'refills_authorized',
        'refills_remaining', 'date_written', 'date_expires', 'date_inactivated', 'original_sig', 'original_sig_expanded',
        'original_days_supply', 'is_verified', 'verified_quantity_dispensed', 'verified_min_days_supply', 'Is_cancelled'
    ];
    protected $guarded = [];
    protected $hidden = ['_token'];

}
