<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'states';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'country_id',
        'is_active'
    ];

    public function getStateName($stateId)
      {
        $name = State::where('id', $stateId)->first('name');
        return $name;
      }
}
