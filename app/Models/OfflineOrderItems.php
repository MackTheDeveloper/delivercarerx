<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class OfflineOrderItems extends Model
{

    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'offline_order_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'offline_order_id',
        'rx_type',
        'rx_number',
        'rx_id',
        'drug_id',
        'drug_name',
        'direction',
        'written_qty',
        'fill_qty',
        'refills',
        'original_days_supply'
    ];

}
