<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefillOrder extends Model
{

    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'refill_orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'newleaf_customer_id',
        'dob',
        'patient_name',
        'hospice_id',
        'hospice_branch_id',
        'pharmacy_id',
        'order_number',
        'newleaf_order_number',
        'rx_id',
        'rx_number',
        'drug_id',
        'drug_name',
        'current_refill_date',
        'last_refill_date',
        'original_rx_date',
        'refill_left',
        'status',
        'shipped_by',
        'tracking_number',
        'shipping_name',
        'address_1',
        'address_2',
        'address_3',
        'city',
        'state',
        'zipcode',
        'shipping_method',
        'shipping_method_code',
        'notes',
        'signature_required',
        'refilled_placed_online',
        'nurse_name',
    ];
}
