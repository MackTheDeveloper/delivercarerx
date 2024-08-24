<?php

namespace App\Repository;

use App\Models\Drugs;
use App\Models\Patients;
use App\Models\OfflineOrder;
use App\Models\OfflineOrderItems;
use App\Models\Rxs;
use Auth;
use DB;
use Illuminate\Support\Carbon;

class OfflineOrderRepository
{
    /**
     * Store hospice information
     * @param array $data
     * @return Response
     */
    public function create($input)
    {
        $placeorderId = '';
        $orderDate = Carbon::createFromFormat('m/d/Y', $input['order_date'])->format('Y-m-d');
        $orderTime = $input['order_time'] ?? "";
        $orderTime = Carbon::parse($orderTime)->format('h:i:s');
        $rph = $input['rph'] ?? "";
        //$phone = $input['phone_number'] ?? "";
        $first_name = $input['first_name'] ?? "";
        $last_name = $input['last_name'] ?? "";
        $dob = date('Y-m-d', strtotime($input['dob'])) ?? "";
        $patient_id = $input['patient_id'] ?? "";
        $is_urgent = $input['urgent'] ? 1 : 0;
        $shipping_address = $input['shipping_address'] ?? "";
        $hospice_name = $input['hospice_name'] ?? "";
        $rn_name_phone_number = $input['rn_name_phone_number'] ?? "";
        $prescriber_and_dea = $input['prescriber_and_dea'] ?? "";
        $prescriber_name = $input['prescriber_name'] ?? "";
        $prescriber_address = $input['prescriber_address'] ?? "";
        $prescriber_state = $input['prescriber_state'] ?? "";
        $prescriber_city = $input['prescriber_city'] ?? "";
        $prescriber_zipcode = $input['prescriber_zipcode'] ?? "";
        $shipping_method = $input['shipping_method'] ?? "";
        $signature_required = $input['signature_required'] == 'Y' ? "Y" : "N";
        $notes = $input['notes'] ?? "";

        $placeOrder = new OfflineOrder();
        $placeOrder->date = $orderDate;
        $placeOrder->time = $orderTime;
        $placeOrder->rph = $rph;
        $placeOrder->firstname = $first_name;
        $placeOrder->lastname = $last_name;
        $placeOrder->dob = $dob;
        $placeOrder->patient_id = $patient_id;
        $placeOrder->shipping_address = $shipping_address;
        $placeOrder->hospice_name = $hospice_name;
        $placeOrder->rn_name_details = $rn_name_phone_number;
        $placeOrder->prescriber_dea = $prescriber_and_dea;
        $placeOrder->prescriber_name = $prescriber_name;
        $placeOrder->prescriber_address = $prescriber_address;
        $placeOrder->prescriber_state = $prescriber_state;
        $placeOrder->prescriber_city = $prescriber_city;
        $placeOrder->prescriber_zip = $prescriber_zipcode;
        $placeOrder->shipping_method = $shipping_method;
        $placeOrder->signature = $signature_required;
        $placeOrder->notes = $notes;
        $placeOrder->rph = $rph;
        $placeOrder->is_urgent = $is_urgent;
        $placeOrder->created_by = Auth::user()->id;
        $placeOrder->created_on = Carbon::now();
        $placeOrder->save();
        $placeorderId = $placeOrder["id"];
        $arr = [];
        $count = 0;
        if ($input['ncr'] && !empty($input['medicine'])) {
            foreach ($input['ncr'] as $key => $val) {
                if ($input['drug_id'][$key]) {
                    $drugNameModel = Drugs::select('description')->where('newleaf_drug_id', $input['drug_id'][$key])->first();
                    if (!empty($drugNameModel)) {
                        $drugName = $drugNameModel->description;
                    } else {
                        $drugName = "";
                    }
                } else {
                    $drugName = $input['medicine'][$key];
                }
                if ($input['medicine'][$key]) {
                    $count++;
                    $arr[$key]['rx_type'] = $input['ncr'][$key];
                    $arr[$key]['drug_name'] = $drugName ?? $input['medicine'][$key];
                    $arr[$key]['direction'] = $input['sig'][$key];
                    $arr[$key]['written_qty'] = $input['fill'][$key];
                    $arr[$key]['fill_qty'] = $input['owed'][$key];
                    $arr[$key]['refills'] = $input['refill'][$key];
                    $arr[$key]['rx_id'] = $input['rx_id'][$key];
                    $arr[$key]['rx_number'] = $input['rx_number'][$key];
                    $arr[$key]['drug_id'] = $input['drug_id'][$key];
                    $arr[$key]['offline_order_id'] = $placeorderId;
                }
            }
            for ($x = array_key_first($arr); $x <= array_key_last($arr); $x++) {
                if (array_key_exists($x, $arr)) {
                    OfflineOrderItems::create($arr[$x]);
                }
            }
        } else {
            return $placeorderId;
        }
        return $placeorderId;
    }

    /**
     * Update hospice information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {
        return '';
    }

    /**
     * Fetch hospice information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return OfflineOrder::where('id', $id)->first();
    }
}
