<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Uds extends Model
{

    use HasFactory, Notifiable;
    protected $table = 'uds';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zip',
        'monday_friday',
        'saturday'
    ];

}
