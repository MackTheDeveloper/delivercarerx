<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Patients;
use Illuminate\Http\Request;
use App\Http\Controllers\API\v1\MainAPIController;
use App\Models\RefillOrder;
use App\Models\RefillShipment;
use App\Models\Rxs;
use DB;

class PatientController extends Controller
{
    protected $service;

    public function __construct(MainAPIController $service)
    {
        $this->service = $service;
    }

    public function list(Request $request)
    {
        $token = $request->header('token');
        $page = $request->input('page', 1);
        $search = $request->input('search');
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        $isValidToken = $this->service->validateToken($token);

        if (!$isValidToken) {
            return response()->json([
                'status' => '498',
                'msg' => 'Invalid token. Please generate a new token.',
            ]);
        }

        $query = Patients::whereNull('deleted_at');
        $query->where(function ($query2) use ($search) {
            $query2->Where(DB::raw("CONCAT(first_name,' ',last_name)"), 'like', '%' . $search . '%')
                ->orWhere('facility_code', 'like', '%' . $search . '%')
                ->orWhere('patients.city', 'like', '%' . $search . '%')
                ->orWhere('patients.state', 'like', '%' . $search . '%');
        });

        $query = $query->offset($offset)
            ->limit($limit)
            ->get();
        $data = [];
        foreach ($query as $key => $value) {
            $id = 'PT' . $value->id;
            $patientName = $value->first_name . ' ' . $value->last_name;
            $facilityBranch  = 'FB' . $value->facility_code;
            if (isset(config('app.patient_shipping_method')[$value->shipping_method])) {
                $shippingMethod = config('app.patient_shipping_method')[$value->shipping_method];
            } else {
                $shippingMethod = config('app.patient_shipping_method')[0];
            }
            if ($value['patient_status'] == 1) {
                $patient_status = 'Active';
            } else if ($value['patient_status'] == 2) {
                $patient_status = 'InFacility';
            } else if ($value['patient_status'] == 3) {
                $patient_status = 'Transfer';
            } else {
                $patient_status = 'Inactive';
            }
            $status = $value->is_active == 1 ? 'Active' : 'Inactive';
            $data[] = [
                'id' => $id,
                'patient_id' => $value->newleaf_customer_id,
                'patient_name' => $patientName,
                'address' => $value->address_1 . ' ' . $value->address_2,
                'city' => $value->city,
                'state' => $value->state,
                'facility_branch' => $facilityBranch,
                'shipping_method' => $shippingMethod,
                'status' => $status,
                'patient_status' => $patient_status,
                'created_at' => getFormatedDate($value->created_at, 'm/d/Y')
            ];
        }
        if (!$query) {
            return response()->json([
                'status' => '500',
                'error' => 'Failed to retrieve data',
            ]);
        }

        return response()->json([
            'status' => '200',
            'msg' => 'Data fetched successfully',
            'data' => $data
        ]);
    }

    public function details(Request $request)
    {
        $token = $request->header('token');
        $id = $request->newleaf_customer_id;
        $isValidToken = $this->service->validateToken($token);

        if (!$isValidToken) {
            return response()->json([
                'status' => '498',
                'msg' => 'Invalid token. Please generate a new token.',
            ]);
        }
        $onlyPatient = Patients::where('newleaf_customer_id', $id)->whereNull('deleted_at')->first();
        if (!$onlyPatient) {
            return response()->json([
                'status' => '500',
                'error' => 'Patient not found',
            ]);
        }
        $rxsData = Rxs::leftJoin('patients', 'rxs.customer_id', '=', 'patients.newleaf_customer_id')
            ->where('patients.newleaf_customer_id', $id)
            ->whereNull('patients.deleted_at')
            ->select('rxs.*')
            ->get();

        $refills = RefillOrder::where('newleaf_customer_id', '=', $id)
            ->select('refill_orders.*')
            ->whereNull('refill_orders.deleted_at')
            ->get();

        $refillShipmentsData = RefillShipment::join('refill_orders', 'refill_shipments.newleaf_order_number', '=', 'refill_orders.newleaf_order_number')
            ->join('branch', 'refill_orders.hospice_branch_id', '=', 'branch.id')
            ->where('refill_orders.newleaf_customer_id', $id)
            ->select([
                'refill_shipments.newleaf_order_number',
                'refill_shipments.created_at',
                'refill_shipments.tracking_number',
                'refill_shipments.courier',
                'branch.code as hospice_name'
            ])
            ->whereNull('refill_shipments.deleted_at')
            ->get();

        foreach ($refills as $key => $value) {
            $address = $value->address_1 . ' ' . $value->city . ' ' . $value->state . ' ' . $value->zipcode;
            $refillsData[] = [
                'patient_name' => $value['patient_name'] ?? '',
                'created_at' => getFormatedDate($value->created_at, 'm/d/Y') ?? '',
                'newleaf_order_number' => $value['newleaf_order_number'] ?? '',
                'status' => $value['status'] ?? '',
                'shipping_method' => $value['shipping_method_code'] ?? '',
                'shipping_method_code' => $value['shipment_method_code'] ?? '',
                'address' => $address ?? '',
            ];
        }
        return response()->json([
            'status' => '200',
            'msg' => 'Data fetched successfully',
            'data' => [
                'details' => $onlyPatient,
                'rxs' => $rxsData,
                'refills' =>  $refillsData,
                'refills_shipment' => $refillShipmentsData,
            ]
        ]);
    }
}
