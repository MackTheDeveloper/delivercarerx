<?php

namespace App\Models;

use Faker\Core\Number;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hospice;
use Illuminate\Database\Eloquent\SoftDeletes;

class NurseBranch extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'nurse_branches';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'hospice_id',
        'facility_id',
        'branch_id',
        'deleted_at'
    ];
    public static function getAllHospiceData($id)
    {
        $hospiceArr = [];
        $hospice_id = [];
        $model = self::where('user_id', $id)->get();
        if ($model) {
            foreach ($model as $key => $value) { {
                    if ($value['hospice_id'] != null) {
                        $hospice_id[$key] = $value['hospice_id'];
                    }
                }
            }
            if (!empty($hospice_id)) {
                foreach ($hospice_id as $key => $value) {
                    $hospiceArr[] = self::getName($value);
                }
                $hospiceArr = array_unique($hospiceArr);
                return (implode(', ', $hospiceArr));
            }
            else
            {
                return "-";
            }
        }
    }
    public static function getName($id)
    {
        if ($id) {
            $value = Hospice::select('code')->where('id', $id)->first();
            if (!empty($value)) {
                return $value->code ?? "-";
            }
        }
    }
    public static function getAllBranchData($id)
    {
        $hospiceArr = [];
        $hospice_id = [];
        $model = self::where('user_id', $id)->get();
        foreach ($model as $key => $value) { {
                if ($value['branch_id'] != null) {
                    $hospice_id[$key] = $value['branch_id'];
                }
            }
        }
        $hospiceArr = array_unique($hospiceArr);
        if (!empty($hospice_id)) {
            foreach ($hospice_id as $key => $value) {
                $hospiceArr[] = self::getBranchName($value);
            }
            return (implode(',', $hospiceArr));
        } else {
            return "-";
        }
    }
    public static function getBranchName($id)
    {
        if ($id) {
            $value = Branch::select('code')->where('id', $id)->first();
            if ($value) {
                return $value->code;
            }
        }
    }
    public static function deletedUnWanted($id, $branchs)
    {
        foreach ($branchs as $key => $value) {
            NurseBranch::where('branch_id', $value)->where('user_id', $id)->forceDelete();
        }
        return 'true';
    }
    public static function getExistingIds($id)
    {
        $model = self::where('user_id', $id)->get();
        $arrId = array();
        foreach ($model as $key => $value) {
            $arrId[$key] = $value['branch_id'];
        }
        $storArray = $arrId;
        foreach ($arrId as $key => $value) {
            self::where('user_id', $id)->where('branch_id', $value)->forceDelete();
        }
        return $storArray;
    }

    public static function getNurseData($branch_id)
    {
        $model = self::where('branch_id', $branch_id)->whereNull('deleted_at')->get();
        $arrId = array();
        foreach ($model as $key => $value) {
            $arrId[$key] = $value['user_id'];
        }
        return array_unique($arrId);
    }

    public function deleteIfExistence($user_id, $branch_id)
    {
        self::where('user_id', $user_id)->where('branch_id', $branch_id)->delete();
    }
}
