<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Hospice extends Model
{

    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'hospice';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'logo',
        'code',
        'address_1',
        'address_2',
        'country_id',
        'state_id',
        'city_id',
        'zipcode',
        'is_active'
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
     * relationship to get country information
     *
     */
    public function country()
    {
        return $this->hasOne(Countries::class, 'id', 'countries');
    }

    /**
     * relationship to get branchs
     *
     */
    public function branches()
    {
        return $this->hasMany(Branch::class, 'hospice_id', 'id');
    }

    public function facilities()
    {
        return $this->hasMany(Facility::class, 'hospice_id', 'id');
    }

    /**
     * get Icon attribute
     * returns the icon
     */
    public function getLogoAttribute($logo)
    {
        $return = asset('assets/img/hospice_logo.png');
        if ($logo) {
            $path = public_path() . '/assets/upload/hospice-logo/' . $logo;
            if (file_exists($path)) {
                $return = url('/assets/upload/hospice-logo/' . $logo);
            }
        }
        return $return;
    }
    public static function getHospiceData($id)
    {

        $value = '';
        $model = self::find($id);
        if ($model) {
            $logo = $model->logo ? $model->logo : 'https://delivercarex.magnetoinfotech.com/assets/img/hospice_logo.png';
            $name = $model->name ? $model->name : '';
            $value = '';
            $value = '<td>
            <img class="rounded-rectangle mr-1" src="' . $logo . '" alt="user" height="35" width="35">
            ' . $name . '
            </td>';
        }
        return $value;
    }
    public static function getNameAndCodeById($id)
    {
        $model = self::find($id);
        return [
            'name' => $model->name,
            'code' => $model->code
        ];
    }
}
