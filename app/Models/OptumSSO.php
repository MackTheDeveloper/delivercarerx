<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptumSSO extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'Optum_sso';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user',
        'email',
        'token',
        'sessionid'
    ];
    protected $hidden = ['_token'];
}
