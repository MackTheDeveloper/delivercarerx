<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefillShipment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'refill_shipments';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'refill_shipment_id','type','saturday_delivery','require_signature','insurance','signature_type','  refill_id','enterprise_order_id','courier','tracking_number','recipient_number','no_of_items','shipment_status','successfully_submitted','error_message','is_trackable','weight','insurance_amount','country_of_manufacture','customs_description','label_location','is_thermal_label','tracking_update_batch_id','fedex_scan_event_code','shipped_on','is_delivered_by_api','remote_fill_order_id','internal_order_num','height','length','width','require_photo_id','packaging_type','weight_units','shipping_fee','created_on','created_by','updated_on','updated_by','created_at','updated_at','deleted_at'
     ];
    protected $guarded = [];
    protected $hidden = ['_token'];

    public function patient()
    {
        return $this->belongsTo(Patients::class, 'newleaf_customer_id', 'newleaf_customer_id');
    }
}
