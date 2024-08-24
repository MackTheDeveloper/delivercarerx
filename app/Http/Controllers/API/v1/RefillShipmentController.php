<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\RefillOrder;
use App\Models\NewLeafOrder;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\API\v1\MainAPIController;

class RefillShipmentController extends Controller
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
        $limit = $request->input('limit', 10);
        $offset = ($page - 1) * $limit;

        $isValidToken = $this->service->validateToken($token);
        
        if (!$isValidToken) {
            return response()->json([
                'status' => '498',
                'msg' => 'Invalid token. Please generate a new token.',
            ]);
        }

        // $query = RefillOrder::whereNull('deleted_at')
        //     ->offset($offset)
        //     ->limit($limit)
        //     ->get();


        $query = NewLeafOrder::selectRaw("newleaf_orders.id, newleaf_orders.patient_id, 
        CAST(order_number AS SIGNED) as order_number, newleaf_orders.tracking_number, 
        newleaf_orders.courier_name, 
        STR_TO_DATE(order_date, '%m/%d/%Y') as order_date, 
        patients.first_name as firstName, patients.last_name as lastName, branch.code as branchCode")
        ->leftJoin('patients', 'patients.newleaf_customer_id', '=', 'newleaf_orders.patient_id')
        ->leftJoin('branch', 'branch.id', '=', 'patients.facility_code')
        ->whereNull('patients.deleted_at')
        ->whereNull('branch.deleted_at');


        $search = $request->input('search');
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(patients.first_name,' ',patients.last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.courier_name', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.order_date', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.tracking_number', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.courier_name', 'like', '%' . $search . '%');
            });
        } 
        $query = $query->groupBy('newleaf_orders.order_number')->offset($offset)->limit($limit)->get()->toArray();
            
        if (empty($query)) {
            return response()->json([
                'status' => '404',
                'msg' => 'No data available',
            ]);
        }
        
        return response()->json([
            'status' => '200',
            'msg' => 'Data fetched successfully',
            'data' => $query
        ]);
    }
    
}
