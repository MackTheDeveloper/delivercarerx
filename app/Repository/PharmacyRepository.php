<?php

namespace App\Repository;

use App\Models\Branch;
use App\Models\Hospice;
use App\Models\Pharmacy;

class PharmacyRepository
{
    /**
     * Store hospice information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        return Pharmacy::create($data);
    }

    /**
     * Update pharmacy information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {
        return Pharmacy::where('id', $id)->update($data);
    }

    /**
     * Fetch pharmacy information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return Pharmacy::where('id', $id)->first();
    }

    /**
     * Get pharmacy facilities from pharmacyId
     * @param $pharmacyId
     * @return array
     */
    // public function getHospiceFacilities($hospiceId)
    // {
    //     return Facilities::where('hospice_id', $hospiceId)->get()->toArray();
    // }

    /**
     * Create hospice user
     * @param array $data
     * @return Response
     */
    public function createPharmacyUser($data)
    {
        $pharmacy = Pharmacy::create($data);
        $id = $pharmacy->id;
        if ($id) {
            $pharmacy->pharmacy_newleaf_id = $id;
            $pharmacy->save();
        }
        return $id;
    }


    /**
     * Fetch store for users
     * @return array
     */
    public function getDropDownList()
    {
        return Pharmacy::select('id', 'name')->whereNull('deleted_at')->where('is_active', 1)->get();
    }

     public function delete($id)
    {
        return Pharmacy::where('id', $id)->delete();
    }
    public function getBranchName($id)
    {
        $returnVal = '[ ';
        foreach ($id as $key => $value)
        {
            $model =  Pharmacy::select('name')->where('id',$value)->first();
            if ($model)
            {
                $returnVal .=' '.$model->name.' ';
            }
        }
        $returnVal .= ' ]';
        return $returnVal;
    }
}
