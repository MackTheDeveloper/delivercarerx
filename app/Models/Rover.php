<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Rover extends Model
{

    use HasFactory, Notifiable;
    protected $table = 'rover';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zip',
        'method',
        'pickup_location'
    ];
}
