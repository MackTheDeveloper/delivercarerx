<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;
    protected $table = 'cities';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'state_id',
        'is_active'
    ];
//Function to get City name
public function getCityName($cityId)
  {
    $name = Cities::where('id', $cityId)->first('name');
    return $name;
  }
}   
