<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NewLeafOrder;
use App\Models\RefillOrder;
use App\Models\RefillShipment;
use App\Service\ActivityService;
use App\Service\AdminService;
use App\Service\BranchService;
use App\Service\RefillsInQueueService;
use App\Service\RefillsInService;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;

class RefillsInController extends Controller
{

    protected $refillsInService,$activityServie,$branchService;

    /**
     * constructor for initialize Admin service
     *
     * @param RefillsInService $refillsInService reference to refillsInQueueService
     *
     */
    public function __construct(RefillsInService $refillsInService, ActivityService $activityServie, BranchService $branchService)
    {
        $this->refillsInService = $refillsInService;
        $this->activityServie = $activityServie;
        $this->branchService = $branchService;
    }

    
    /**
     * Listing of the refills
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $branch = $this->branchService->getDropDownListBranchAndHospice();
        return view('admin.refillsIn.refills-listing', compact('branch'));
    }

    // Used for Orders->All Orders Received
    public function list(Request $request)
    {
        $result = $this->refillsInService->fetchListing($request);
        return Response::json($result);
    }

    public function refillOrderItemsindex($orderNumber)
    {
    //dd($orderNumber);
	$patientName = '';
        // We must pass the patient name to the blade
       /*$queryToFetchName = RefillOrder::select('patient_name','newleaf_order_number','patient_id')
        ->where('refill_orders.order_number',$orderNumber)
        ->get()->toArray();*/

        $queryToFetchName = RefillShipment::select('patients.first_name AS PFname','patients.last_name AS PLname','refills.refill_number AS refillNumber','refill_shipments.newleaf_order_number AS orderNumber','rxs.rx_number AS rxNumber','rxs.rx_id AS rxNewLeafId','drugs.description AS drugDescription','refills.date_filled AS dateRefilled','rxs.date_written AS rxDate')->where('newleaf_order_number',$orderNumber)
        ->leftJoin('refills', 'refills.refill_id', 'refill_shipments.refill_id')
        ->leftJoin('rxs', 'rxs.rx_id', 'refills.rx_id')
        ->leftJoin('drugs', 'drugs.newleaf_drug_id', 'refills.drug_id')
        ->leftJoin('patients', 'patients.newleaf_customer_id', 'rxs.customer_id')
        ->get()->toArray();
        //dd($queryToFetchName);


        /*
        
        $queryToFetchName = RefillShipment::select('patients.first_name AS PFname','patients.last_name AS PLname',
        'refills.refill_number AS refillNumber','refill_shipments.newleaf_order_number AS orderNumber','rxs.rx_number AS rxNumber',
        'rxs.rx_id AS rxNewLeafId','drugs.description AS drugDescription','refills.date_filled AS dateRefilled','rxs.date_written AS rxDate', 
        'refill_orders.order_number', 'refill_orders.patient_name', 'refill_orders.newleaf_order_number')
        ->where('refill_shipments.newleaf_order_number',$orderNumber)
        ->leftJoin('refills', 'refills.refill_id', 'refill_shipments.refill_id')
        ->leftJoin('rxs', 'rxs.rx_id', 'refills.rx_id')
        ->leftJoin('drugs', 'drugs.newleaf_drug_id', 'refills.drug_id')
        ->leftJoin('patients', 'patients.newleaf_customer_id', 'rxs.customer_id')
        ->leftJoin('refill_orders', 'refill_orders.newleaf_order_number', 'refills.refill_id')
        ->get()->toArray();

        */


        foreach($queryToFetchName as $key => $value){
           $fName = $value['PFname'];
           $lName = $value['PLname'];

           $patientName = $fName . ' ' . $lName;
        }

        // Is expecting order_number on this path admin/refillOrderItems/index/{number}
        return view('admin.refillsIn.refills-item-listing',compact('orderNumber','patientName'));
        //return view('admin.refillsIn.refills-item-listing',compact('orderNumber'));
    }

    public function refillOrderItemslist(Request $request)
    {
        $result = $this->refillsInService->fetchItemListing($request);
        return Response::json($result);
    }



}
