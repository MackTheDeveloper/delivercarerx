<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\v1\MainAPIController;
use App\Models\Branch;
use App\Models\Cart;
use App\Models\Drugs;
use App\Models\Facility;
use App\Models\NurseBranch;
use App\Models\PatientAddresses;
use App\Models\Patients;
use App\Models\Refill;
use App\Models\RefillOrder;
use App\Models\RefillOrderItems;
use App\Models\Rxs;
use App\Models\User;
use Carbon\Carbon;

class RefillsController extends Controller
{
    protected $service;

    public function __construct(MainAPIController $service)
    {
        $this->service = $service;
    }

    public function details(Request $request)
    {
        $token = $request->header('token');
        $rx_number = $request->input('rx_number') ?? 0;
        $patient_id = $request->input('patient_id') ?? 0;
        $isValidToken = $this->service->validateToken($token);

        if (!$isValidToken) {
            return response()->json([
                'status' => '498',
                'msg' => 'Invalid token. Please generate a new token.',
            ]);
        }

        $query = RefillOrder::leftJoin('refill_order_items', function ($join) {
            $join->on('refill_orders.id', '=', 'refill_order_items.refill_order_id');
        })
            ->where('refill_orders.patient_id', $patient_id)
            ->where('refill_order_items.rx_number', $rx_number)
            ->whereNull('refill_orders.deleted_at')
            ->get();


        if (!$query) {
            return response()->json([
                'status' => '500',
                'error' => 'Failed to retrieve data',
            ]);
        }
        return response()->json([
            'status' => '200',
            'msg' => 'Data fetched successfully',
            'data' => $query
        ]);
    }

    public function list(Request $request)
    {
        $token = $request->header('token');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $hospiceBranch = $request->input('hospice_branch');
        $status = $request->input('status');
        $searchQuery = $request->input('search');
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 100);
        $offset = ($page - 1) * $limit;
        $isValidToken = $this->service->validateToken($token);

        if (!$isValidToken) {
            return response()->json([
                'status' => '498',
                'msg' => 'Invalid token. Please generate a new token.',
            ]);
        }
        $query = RefillOrder::whereNull('deleted_at');
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('patient_name', 'like', "%$searchQuery%")
                    ->orWhere('newleaf_order_number', 'like', "%$searchQuery%")
                    ->orWhere('order_number', 'like', "%$searchQuery%")
                    ->orWhere('tracking_number', 'like', "%$searchQuery%");
            });
        }

        if ($startDate && $endDate) {
            $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($hospiceBranch) {
            $branchId = Branch::where('name', $hospiceBranch)->value('id');
            $query->where('hospice_branch_id', $branchId);
        }
        $query->offset($offset)->limit($limit);
        $result = $query->get();
        if ($result->isEmpty()) {
            return response()->json([
                'status' => '500',
                'error' => 'Failed to retrieve data',
            ]);
        }

        return response()->json([
            'status' => '200',
            'msg' => 'Data fetched successfully',
            'data' => $result,
        ]);
    }

    public function placeRefillOrder(Request $request)
    {
        $token = $request->header('token');
        if (!$this->service->validateToken($token)) {
            return response()->json(['status' => 498, 'msg' => 'Invalid token. Please generate a new token.']);
        }
        $request->validate([
            'email' => 'required|email',
            'patient_id' => 'required',
            'rx_numbers' => 'required',
            'shipping_method' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('user_type', 2)
            ->where('hospice_user_role', 3)
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            return response()->json(['status' => 500, 'msg' => 'Nurse account does not exist in the system.']);
        }

        $patient = Patients::where('newleaf_customer_id', $request->patient_id)
            ->where('is_active', 1)
            ->where('isActive', "True")
            ->whereNull('deleted_at')
            ->first();

        if (!$patient) {
            return response()->json(['status' => 500, 'msg' => 'Patient ID is invalid.']);
        }

        $nurseBranchAssignment = NurseBranch::where('user_id', $user->id)
            ->where('branch_id', $patient->facility_code)
            ->first();

        if (!$nurseBranchAssignment) {
            return response()->json(['status' => 500, 'msg' => 'Nurse is not assigned to the patients branch.']);
        }

        $patient_address = PatientAddresses::where('newleaf_customer_id', $request->patient_id)
            ->where('is_primary', 1)
            ->where('isPrimary', 'True')
            ->first();

        $rxData = [];
        $rxNumbers = [];
        $rxTypes = [];

        $rxNumberTypePairs = explode(',', $request->input('rx_numbers'));

        foreach ($rxNumberTypePairs as $pair) {
            if (strpos($pair, '-') !== false) {
                list($rxNumber, $rxType) = explode('-', $pair);
                $rxTypes[] = $rxType;
            } else {
                $rxNumber = $pair;
                $rxTypes[] = 'R';
            }
            $rxNumbers[] = $rxNumber;
        }

        $invalidRxNumbers = [];

        foreach ($rxNumbers as $rxNumber) {
            if (!Rxs::where('customer_id', $request->patient_id)
                ->where('rx_number', trim($rxNumber))
                ->exists()) {
                $invalidRxNumbers[] = $rxNumber;
            }
        }

        if (!empty($invalidRxNumbers)) {
            return response()->json(['status' => 500, 'msg' => 'Invalid Rx Numbers: ' . implode(', ', $invalidRxNumbers)]);
        }

        $shippingMethods = config('app.shipping_methods');
        $last_order_number = RefillOrder::selectRaw('refill_orders.order_number')
            ->orderBy('id', 'DESC')
            ->first();

        $orderNumber = $last_order_number ? str_pad($last_order_number->order_number + 1, 5, "0", STR_PAD_LEFT) : "00001";

        $branch = Branch::where('id', $patient->facility_code)->first();

        $refillOrder = RefillOrder::create([
            'patient_id' => $patient->id,
            'newleaf_customer_id' => $patient->newleaf_customer_id,
            'dob' => $patient->dob,
            'patient_name' => $patient->first_name . ' ' . $patient->last_name,
            'hospice_id' => $branch->hospice_id,
            'hospice_branch_id' => $branch->id,
            'pharmacy_id' => $patient->pharmacy_id,
            'order_number' => $orderNumber,
            'status' => 2,
            'shipping_name' => $patient->first_name . ' ' . $patient->last_name,
            'address_1' => $patient_address->address_1,
            'address_2' => $patient_address->address_2,
            'city' => $patient_address->city,
            'state' => $patient_address->state,
            'zipcode' => $patient_address->zipcode,
            'shipping_method' => $shippingMethods[$request->shipping_method],
            'shipping_method_code' => $request->shipment_method,
            'notes' => $request->notes,
            'signature_required' => $request->signature_required,
            'refilled_placed_online' => 'N',
            'nurse_name' => $user->name,
        ]);

        foreach ($rxNumbers as $key => $rxNumber) {
            $rxType = $rxTypes[$key];
            $rxData = Rxs::where('rx_number', $rxNumber)->first();
            $date1 = Carbon::createFromFormat('m/d/Y', date("m/d/Y"));
            $date2 = Carbon::createFromFormat('m/d/Y', date('m/d/Y', strtotime($rxData->date_expires)));
            $dateResult = $date2->gt($date1);
            $is_cancelled_bool = $rxData->Is_cancelled == "False";
            $refillRemaining = $rxData->refills_remaining > 0;
            $status = ($rxData->status == 9) ? false : true;
            if (!$dateResult || !$is_cancelled_bool || !$refillRemaining || !$status) {
                return response()->json(['status' => 500, 'msg' => 'Rx Number is not refillable']);
            }
            $drugData = Drugs::where('newleaf_drug_id', $rxData->prescribed_drug_id)->first();
            $lastRefill = RefillOrderItems::select('refill_order_items.created_at')
                ->join('refill_orders', 'refill_orders.id', 'refill_order_items.refill_order_id')
                ->where('refill_orders.rx_number', $rxNumber)
                ->where('patient_id', $patient->newleaf_customer_id)
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->first();
            $refillQty = Refill::selectRaw('refills.dispensed_quantity')->where('rx_id', $rxData->rx_id)->where('refill_number', 0)->first();
            RefillOrderItems::create([
                'refill_order_id' => $refillOrder->id,
                'rx_id' => $rxData->id,
                'rx_number' => $rxData->rx_number,
                'drug_id' => $drugData->newleaf_drug_id,
                'drug_name' => $drugData->description,
                'direction' => $rxData->original_sig,
                'current_refill_date' => date("Y-m-d"),
                'last_refill_date' => $lastRefill,
                'original_rx_date' => $rxData->date_written,
                'refill_left' => $rxData->refills_remaining,
                'quantity' => empty($refillQty->dispensed_quantity) ? 0 : $refillQty->dispensed_quantity,
                'created_at' => $rxData->created_at,
                'updated_at' => $rxData->updated_at,
                'rx_type' => $rxType
            ]);
        }
        return response()->json(['status' => 200, 'msg' => 'Order Placed successfully.', 'order_id' => $refillOrder->id]);
    }
}
