<?php

namespace App\Repository;

use App\Models\Drugs;
use Auth;

class DrugsRepository
{
    /**
     * Store drugs information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        try {
            $value = Drugs::create($data);
            return $value;
        } catch (QueryException $e) {
            dd($e);
        }
        
    }

    /**
     * Update drugs information
     * @param array $data
     * @return Response
     */
     public function update($data, $id = null)
    {
        $model = Drugs::find($id);
        $model->identifier = $data['identifier'];
        $model->description = $data['description'];
        $model->strength = $data['strength'];
        $model->new_ndc = $data['new_ndc'];
        $model->manufacturer_name = $data['manufacturer_name'];
        $model->is_generic = $data['is_generic'];
        $model->is_rx = $data['is_rx'];
        $model->status_code = $data['status_code'];
        $model->dosage_form_code = $data['dosage_form_code'];
        $model->direct_source = $data['direct_source'];
        $model->master_description = $data['master_description'];
        
        if($model->update())
        {
            return 'success';
        }
        else {
            return 'failed';
        }
    }
    /**
     * Fetch drugs information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return Drugs::where('id', $id)->first();
    }


    /**
     * delete drugs
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return Drugs::where('id', $id)->delete();
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
        $data = Drugs::select($returnValue)->where($fieldName, $fieldValue)->first();
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
