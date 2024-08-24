<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pharmacy extends Model
{
    use HasFactory,SoftDeletes;

     protected $table = 'pharmacy';

      protected $fillable = [
        'name',
        'address_1',
        'address_2',
        'state_id',
        'city_id',
        'country_id',
        'zipcode',
        'newleaf_endpoint',
        'newleaf_port',
        'newleaf_username',
        'newleaf_password',
        'roverAPI_user',
        'roverAPI_password',
        'roverApi_accountnumber',
        'google_link','is_active'
    ];
    /**
     * relationship to get city information
     *
     */
    public function city()
    {
        return $this->hasOne(Cities::class, 'id', 'city');
    }

    /**
     * relationship to get state information
     *
     */
    public function state()
    {
        return $this->hasOne(State::class, 'id', 'state');
    }
      /**
     * relationship to get countries information
     *
     */
    public function country()
    {
        return $this->hasOne(Countries::class, 'id', 'countries');
    }

    public static function getDropDownList()
    {
        return self::select('id','name')->whereNull('deleted_at')->where('is_active',1)->get();
    }

    public static function getNameById($id)
    {
        $arrayId = explode(',',$id);
        $returnArray = array();
        foreach($arrayId as $arrayId)
        {
            $returnArray[] = Pharmacy::returnName($arrayId);
        }
        return $returnArray;
    }
    public static function returnName($id)
    {
        return self::where('id',$id)->pluck('name')->first();
    }
     protected $dates = ['deleted_at'];
}




