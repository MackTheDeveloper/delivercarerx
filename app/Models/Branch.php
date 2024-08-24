<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Branch extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    protected $table = 'branch';
    protected $guarded = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'hospice_id',
        'facility_id',
        'address_1',
        'address_2',
        'country_id',
        'state_id',
        'city_id',
        'zipcode',
        'phone',
        'carrier',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at'
    ];


    public static function rules($id = null)
    {
        return [
            'name' => 'bail|required',
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
        return $this->belongsTo(Hospice::class, 'hospice_id')->WhereNull('deleted_at');
    }

    public function facility()
    {
        return $this->belongsTo(Facilities::class, 'facility_id')->WhereNull('deleted_at');
    }

    public static function getBranchAndHospiceIds($id)
    {
        $model = self::find($id);
        if ($model) {
            return [
                'branch_id' => $model->id,
                'hospice_id' => $model->hospice_id,
                'facility_id' => $model->facility_id,
            ];
        }
    }
    public static function getBranchAndHospiceIdsWithCode($code = "")
    {
        if($code){
            $model = self::where('code', $code)->first();
            if ($model) {
                return [
                    'branch_id' => $model->id,
                    'hospice_id' => $model->hospice_id,
                    'facility_id' => $model->facility_id,
                ];
            }
        }
        
    }
    public static function getIdsByBranchCode($codes = [])
    {
        $ids = [];
        if (!empty($codes)) {
            foreach (explode(',', $codes) as $key => $value) {
                $model = self::where('code', $value)->first();
                if ($model) {
                    $ids[] = $model->id;
                }
            }
            return implode(',', $ids);
        }
    }

    public static function getAllIdRelatedToHospiceId()
    {
        $hospiceId = Auth::user()->hospice_id;
        if ($hospiceId) {
            $id = [];
            $ids = self::where('hospice_id', $hospiceId)->get()->toArray();
            foreach ($ids as $key => $val) {
                $id[$key] = $val["id"];
            }
            return implode(',', $id);
        }
    }
    public static function getBranchData($ids)
    {
        if (!empty($ids)) 
        {
            $model = self::whereIn('id', $ids)->pluck('name')->toArray();
            if ($model)
            {
                return implode(',', $model);
            }    
        }
    }
}
