<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefillOrderItems extends Model
{
    use HasFactory;

     protected $table = 'refill_order_items';

      protected $fillable = [
        'refill_order_id',
        'rx_id',
        'rx_number',
        'drug_id',
        'drug_name',
        'direction',
        'current_refill_date',
        'last_refill_date',
        'original_rx_date',
        'refill_left',
        'quantity',
        'rx_type',
        'created_at',
        'updated_at',
    ];

}




