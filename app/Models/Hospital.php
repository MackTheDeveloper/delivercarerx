<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;
    protected $table = 'hospital';
    protected $guarded = [];



    public static function rules($id = null)
    {
        return [
            'name'=>'bail|required',
            'code' => 'bail|required',
            'hospice_id' => 'bail|required',
            'facility_id' => 'bail|required',
            'address_1' => 'bail|required',
            'address_2' => 'bail|required',
            'country_id' => 'bail|required',
            'state_id' => 'bail|required',
            'city_id' => 'bail|required',
            'zipcode' => 'bail|required',
            'phone' => 'bail|required',
            'status' => 'bail|required'
        ];
    }

    public function hospice()
    {
    return $this->belongsTo(Hospice::class,'hospice_id');
    }

    public function facility()
    {
    return $this->belongsTo(Facilities::class,'facility_id');
    }
}


