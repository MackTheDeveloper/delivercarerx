<?php

namespace App\Repository;

use App\Models\Refill;
use App\Models\RefillAdjudcation;
use App\Models\RefillShipment;
use Auth;

class RefillAdjudicationsRepository
{
    /**
     * Store patient information
     * @param array $data
     * @return Response
     */
    public function create($data)
    { //dd($data);
        try {
            $value = RefillAdjudcation::create($data); 
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
        $model = RefillAdjudcation::find($id);
        $model->refill_plan_order = $data['refill_plan_order'];
        $model->third_party_id = $data['third_party_id'];
        $model->adjudication_type = $data['adjudication_type'];
        
        $model->print_copies = $data['print_copies'];
        $model->print_monograph = $data['print_monograph'];
        $model->workstation_name = $data['workstation_name'];
        $model->refill_adjudication_status = $data['refill_adjudication_status'];
        $model->refill_id = $data['refill_id'];
        $model->customer_id = $data['customer_id'];
        $model->claim_data = $data['claim_data'];
        $model->customer_ar_account_id = $data['customer_ar_account_id'];
        $model->reset_aging = $data['reset_aging'];
        $model->updated_by = $data['updated_by'];
        $model->updated_on = $data['updated_on'];
        
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
        return RefillAdjudcation::where('id', $id)->first();
    }


    /**
     * delete patient
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return RefillAdjudcation::where('id', $id)->delete();
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
        $data = RefillAdjudcation::select($returnValue)->where($fieldName, $fieldValue)->first();
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
