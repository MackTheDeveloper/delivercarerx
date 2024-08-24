<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Drugs;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\API\v1\MainAPIController;

class DrugsController extends Controller
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

        $query = Drugs::whereNull('deleted_at');
           
        $search = $request->input('search');
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("description"), 'like', '%' . $search . '%');
            });
        } 
        $query = $query->offset($offset)->limit($limit)->get();   

        // $totalCount = Drugs::whereNull('deleted_at')->count();
        
        if ($query->isEmpty()) {
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