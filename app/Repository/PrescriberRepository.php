<?php

namespace App\Repository;

use App\Models\Hospice;
use App\Models\Prescriber;
use Auth;

class PrescriberRepository
{
    /**
     * Store patient information
     * @param array $data
     * @return Response
     */
    public function create($data)
    { //dd($data);
        try {
            $value = Prescriber::create($data); 
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
        $model = Prescriber::find($id);
        $model->first_name = $data['first_name'];
        $model->middle_name = $data['middle_name'];
        $model->last_name = $data['last_name'];
        
        $model->is_active = $data['is_active'];
        $model->prescriber_status = $data['prescriber_status'];
        
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
        return Prescriber::where('id', $id)->first();
    }


    /**
     * delete patient
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return Prescriber::where('id', $id)->delete();
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
        $data = Prescriber::select($returnValue)->where($fieldName, $fieldValue)->first();
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
