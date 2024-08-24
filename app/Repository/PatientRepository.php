<?php

namespace App\Repository;

use App\Models\Hospice;
use App\Models\Patients;
use Auth;

class PatientRepository
{
    /**
     * Store patient information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        try {
            $value = Patients::create($data);
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
        $model = Patients::find($id);
        $model->first_name = $data['first_name'];
        $model->middle_name = $data['middle_name'];
        $model->last_name = $data['last_name'];
        $model->facility_code = $data['facility_code'];
        $model->address_1 = $data['address_1'];
        $model->address_2= $data['address_1'];
        $model->country = $data['country'];
        $model->state = $data ['state'];
        $model->city = $data['city'];
        $model->zipcode= $data['zipcode'];
        $model->phone_number = $data['phone_number'];
        $model->dob= $data['dob'];
        $model->patient_id = $data['patient_id'];
        $model->is_active = $data['is_active'];
        $model->patient_status = $data['patient_status'];
        $model->shipping_method = $data['shipping_method'];
        $model->gender = $data['gender'];
        if($model->update())
        {
            return 'success';
        }
        else {
            return 'failed';
        }

    }
    /**
     * Fetch patient information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return Patients::where('id', $id)->first();
    }


    /**
     * delete patient
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return Patients::where('id', $id)->delete();
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
        $data = Patients::select($returnValue)->where($fieldName, $fieldValue)->first();
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
