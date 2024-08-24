<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    protected $table = 'facilities';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hospice_id',
        'hospice_client',
        'hospice_group',
        'email',
        'address_1',
        'address_2',
        'state_id',
        'zipcode',
        'email'



    ];

    public static function getFacilityData($facility)
    {
        return self::select('name')->where('id', $facility)->first();
    }
}
