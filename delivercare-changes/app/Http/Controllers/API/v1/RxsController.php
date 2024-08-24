<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Rxs;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\API\v1\MainAPIController;

class RxsController extends Controller
{
    protected $service;

    public function __construct(MainAPIController $service)
    {
        $this->service = $service;
    }

    public function list(Request $request, $id)
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
        $query = Rxs::where('customer_id', $id);
           
        $search = $request->input('search');
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("rx_number"), 'like', '%' . $search . '%');
            });
        } 
        $query = $query->offset($offset)->limit($limit)->get();   

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
