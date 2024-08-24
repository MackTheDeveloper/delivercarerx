<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class Partners extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'partners';

      protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'username',
        'zipcode',
        'password',
        'status',
        'token',
        'expiry_at',
        'notes',
        
    ];
}
