<?php

namespace App\Repository;

use App\Models\Hospice;
use App\Models\PatientAddresses;
use App\Models\Patients;
use Auth;

class PatientAddressesRepository
{
    /**
     * Store patient information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        try {
            $value = PatientAddresses::create($data);
            return $value;
        } catch (QueryException $e) {
            dd($e);
        }
        
    }

    /**
     * Update patient information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {
        return PatientAddresses::where('id', $id)->update($data);

    }
    /**
     * Fetch patient information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return PatientAddresses::where('id', $id)->first();
    }


    /**
     * delete patient
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return PatientAddresses::where('id', $id)->delete();
    }

    public function getAttrById($id, $attr)
    {
        $return = "";
        $data = self::select($attr)->where('id', $id)->first();
        if ($data) {
            $return = $data->$attr;
        }
        return $return;
    }

    public function checkRecordExistBySpecificField($fieldName, $fieldValue, $returnValue)
    {
        $return = array();
        $data = PatientAddresses::select($returnValue)->where($fieldName, $fieldValue)->first();
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
