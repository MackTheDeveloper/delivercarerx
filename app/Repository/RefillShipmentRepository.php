<?php

namespace App\Repository;

use App\Models\Refill;
use App\Models\RefillAdjudcation;
use App\Models\RefillShipment;
use Auth;

class RefillShipmentRepository
{
    /**
     * Store patient information
     * @param array $data
     * @return Response
     */
    public function create($data)
    { //dd($data);
        try {
            $value = RefillShipment::create($data); 
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
        $model = RefillShipment::find($id);
        $model->type = $data['type'];
        $model->saturday_delivery = $data['saturday_delivery'];
        $model->require_signature = $data['require_signature'];
        
        $model->insurance = $data['insurance'];
        $model->signature_type = $data['signature_type'];
        $model->refill_id = $data['refill_id'];
        $model->enterprise_order_id = $data['enterprise_order_id'];
        $model->tracking_number = $data['tracking_number'];
        $model->recipient_number = $data['recipient_number'];
        $model->no_of_items = $data['no_of_items'];
        $model->shipment_status = $data['shipment_status'];
        $model->successfully_submitted = $data['successfully_submitted'];
        $model->error_message = $data['error_message'];
        $model->is_trackable = $data['is_trackable'];
        $model->weight = $data['weight'];
        $model->insurance_amount = $data['insurance_amount'];
        $model->country_of_manufacture = $data['country_of_manufacture'];
        $model->customs_description = $data['customs_description'];
        $model->label_location = $data['label_location'];
        $model->is_thermal_label = $data['is_thermal_label'];
        $model->tracking_update_batch_id = $data['tracking_update_batch_id'];
        $model->fedex_scan_event_code = $data['fedex_scan_event_code'];
        $model->shipped_on = $data['shipped_on'];
        $model->is_delivered_by_api = $data['is_delivered_by_api'];
        $model->remote_fill_order_id = $data['remote_fill_order_id'];
        $model->internal_order_num = $data['internal_order_num'];
        $model->height = $data['height'];
        $model->length = $data['length'];
        $model->width = $data['width'];
        $model->require_photo_id = $data['require_photo_id'];
        $model->packaging_type = $data['packaging_type'];
        $model->weight_units = $data['weight_units'];
        $model->shipping_fee = $data['shipping_fee'];
        $model->created_on = $data['created_on'];
        $model->created_by = $data['created_by'];
        $model->updated_on = $data['updated_on'];
        $model->updated_by = $data['updated_by'];

        
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
        return RefillShipment::where('id', $id)->first();
    }


    /**
     * delete patient
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return RefillShipment::where('id', $id)->delete();
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
        $data = RefillShipment::select($returnValue)->where($fieldName, $fieldValue)->first();
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
