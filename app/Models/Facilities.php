<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class Facilities extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'facilities';



      protected $fillable = [
        
        'hospice_id',
        'hospice_client',
        'hospice_group',
         'name',
        'email',
        'address_1',
        'address_2',
        'city_id',
        'state_id',
        'zipcode',
        'email',
        
    ];

    
     public static function getBranchAndHospiceIds($id)
    {
        $model = self::find($id);
        if ($model) {
            return [
                'hospice_id' => $model->hospice_id,
                'facility_id' => $model->facility_id,
            ];
        }
    }

     public static function getFacilityData($facility)
    {
        return self::select('name')->where('id', $facility)->first();
    }
    

    public function hospice()
    {
    return $this->belongsTo(Hospice::class,'hospice_id');
    }
    
    public function pharmacy()
    {
    return $this->belongsTo(Pharmacy::class,'pharmacy_id');
    }

}
