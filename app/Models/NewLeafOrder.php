<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NewLeafOrder extends Model
{

    use HasFactory, Notifiable;
    protected $table = 'newleaf_orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'order_number',
        'tracking_number',
        'courier_name',
        'shipped_by',
        'order_date'
    ];

}
