<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Shipping extends Model
{

    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'shipping';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'logo',
        'url',
        'tracking_url',
        'tracking_prefix',
        'tracking_length',
        'tracking_suffix'
    ];

     public function getLogoAttribute($logo)
    {
        $return = url('https://delivercarex.magnetoinfotech.com/assets/img/hospice_logo.png');
        $path = public_path() . '/assets/upload/shipping-logo/' . $logo;
        if (file_exists($path)) {
            $return = url('/assets/upload/shipping-logo/' . $logo);
        }
        return $return;
    }

    public static function getTrackingUrl($id)
    {
        $model = self::where('id',$id)->first();
       if ($model){
           return $model->tracking_url;
       }
    }

    public static function getLogo($id)
    {
       $model = self::where('id',$id)->first();
       if ($model){
           return $model->logo;
       }
    }

}
