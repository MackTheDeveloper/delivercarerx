<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'gender',
        'password',
        'role_id',
        'is_active',
        'first_name',
        'last_name',
        'name',
        'user_logon_name',
        'street',
        'job_title',
        'department',
        'company',
        'manager',
        'description',
        'office',
        'initials',
         'email',
        'user_type',
        'hospice_user_role',
        'hospice_id',
        'facility_id',
        'phone',
        'timezone',
        'pharmacy_id',
        'address1',
        'address2',
        'country_id',
        'state_id',
        'city_id',
        'zipcode',
        'branch_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->hasOne(Roles::class, 'id', 'role_id');
    }

    public static function getAllData()
    {
        $finalArr = array();
        $data = self::select('id','first_name', 'name', 'profile_picture', 'last_name', 'role_id', 'email', 'is_active', 'created_at', 'phone','pharmacy_id')->where('user_type', '1')->whereNull('deleted_at')->get();
        foreach ($data as $key => $value) {
            $finalArr[$key]['id'] = $value['id'];
            $finalArr[$key]['profile_picture'] = $value['profile_picture'];
            if ($value['name']) {
                $finalArr[$key]['name'] = $value['name'];
            } else {
                $finalArr[$key]['name'] = $value['first_name'] . ' ' . $value['last_name'];
            }
            $finalArr[$key]['email'] = $value['email'];
            $finalArr[$key]['phone'] = $value['phone'];
            $finalArr[$key]['status'] = $value['is_active'];
            $finalArr[$key]['created_at'] = $value['created_at'];
            if ($value['role_id'] == 1) {
                $finalArr[$key]['role_name'] = 'Site Admin';
            } else if ($value['role_id'] == 2) {
                $finalArr[$key]['role_name'] = 'Pharmacy Admin';
            } else {
                $finalArr[$key]['role_name'] = 'User';
            }
            $finalArr[$key]['pharmacy_id'] = $value['pharmacy_id'];
        }
        return $finalArr;
    }

  /*  public function getFirstNameAttribute() 
{
    return $this->userDetails->first_name;
}*/

}
