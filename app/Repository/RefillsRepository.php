<?php

namespace App\Repository;

use App\Models\Refill;
use App\Models\RefillAdjudcation;
use App\Models\RefillShipment;
use Auth;

class RefillsRepository
{
    /**
     * Store patient information
     * @param array $data
     * @return Response
     */
    public function create($data)
    { //dd($data);
        try { 
            $value = Refill::create($data); 
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
    { //dd($data);
        $model = Refill::find($id);
        $model->refill_number = $data['refill_number'];
        $model->rx_id = $data['rx_id'];
        $model->drug_id = $data['drug_id'];
        $model->destination_type_id = $data['destination_type_id'];
        $model->destination_date = $data['destination_date'];
        $model->customer_address_id = $data['customer_address_id'];
        $model->status = $data['status'];
        $model->facility_address_id = $data['facility_address_id'];
        $model->package_choice = $data['package_choice'];
        $model->date_filled = $data['date_filled'];
        $model->sig = $data['sig'];
        $model->sig_expanded = $data['sig_expanded'];
        $model->destination_notes = $data['destination_notes'];
        $model->dispensed_quantity = $data['dispensed_quantity'];
        $model->days_supply = $data['days_supply'];
        $model->min_days_supply = $data['min_days_supply'];
        $model->max_days_supply = $data['max_days_supply'];
        $model->number_of_pieces = $data['number_of_pieces'];
        $model->rph_user_name = $data['rph_user_name'];
        $model->rph_user_id = $data['rph_user_id'];
        $model->is_ordered = $data['is_ordered'];
        $model->is_dispensed = $data['is_dispensed'];
        $model->is_prefill = $data['is_prefill'];
        $model->discard_after_date = $data['discard_after_date'];
        $model->workflow_status = $data['workflow_status'];
        $model->number_of_labels = $data['number_of_labels'];
        $model->doses_per_day = $data['doses_per_day'];
        $model->units_per_dose = $data['units_per_dose'];
        $model->destination_address1 = $data['destination_address1'];
        $model->destination_address2 = $data['destination_address2'];
        $model->destination_city = $data['destination_city'];
        $model->destination_state = $data['destination_state'];
        $model->destination_zip = $data['destination_zip'];
        $model->effective_date = $data['effective_date'];
        $model->prescriber_address_id = $data['prescriber_address_id'];
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
        return Refill::where('id', $id)->first();
    }


    /**
     * delete patient
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return Refill::where('id', $id)->delete();
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
        $data = Refill::select($returnValue)->where($fieldName, $fieldValue)->first();
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
