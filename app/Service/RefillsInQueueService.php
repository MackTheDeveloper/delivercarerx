<?php

namespace App\Service;

use App\Models\RefillOrder;
use App\Models\Rxs;
use App\Models\RefillOrderItems;
use App\Models\NurseBranch;
use App\Repository\ActivityRepository; 
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use App\Repository\RefillsInQueueRepository;
use Hash;
use DB;
use Auth;
use Carbon\Carbon;

class RefillsInQueueService
{

    protected $refillsInQueueRepository, $userRepo, $activityRepo;

    /**
     * @param UserRepository $userRepo reference to userRepo
     * @param ActivityRepository $activityRepo reference to activityRepo
     * 
     */
    public function __construct(UserRepository $userRepo, ActivityRepository $activityRepo, RefillsInQueueRepository $refillsInQueueRepository)
    {
        $this->refillsInQueueRepository = $refillsInQueueRepository;
        $this->userRepo = $userRepo;
        $this->activityRepo = $activityRepo;
    }

    /** 
     * Fetch hospice information
     * @param $id
     */
    public function fetchInformation($id)
    {
        return $this->hospiceRepo->fetch($id);
    }

    public function fetchingPatientListing($id)
    {
        return $this->refillsInQueueRepository->fetchPatientDetails($id);
    }
    /** 
     * Add refills in queue information
     * @param object $request
     */
       public function fetchListing($request)
    {
        $req = $request->all();
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        $orderby = ['refill_orders.patient_id', 'refill_orders.patient_name', 'refill_orders.order_number', 'refill_order_items.rx_number', 'refill_order_items.drug_name', 'current_refill_date', 'last_refill_date', 'original_rx_date', 'refill_left','shipping_method','signature_required','refilled_placed_online'];
        $totalRecords = RefillOrderItems::selectRaw('count(*) as total')
                ->join('refill_orders', 'refill_orders.id', 'refill_order_items.refill_order_id')
                ->whereIn('status',[1,2]);

        if($column == 0)
        {
            $column = 2;
            $order = "desc";
        }
        
        // pharmacy admin or delivercare user
        if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
        {
            $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
            $totalRecords->whereIn('refill_orders.pharmacy_id',$pharmacy_id);
        }
        // hospice admin
        if(Auth::user()->user_type == 2 && Auth::user()->role_id == 1)
        {
            $hospice_id = Auth::user()->hospice_id;
            $totalRecords->where('refill_orders.hospice_id',$hospice_id);
        }
        // branch admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
        {
            $branch_id = explode(",",Auth::user()->branch_id);

            if(!empty(Auth::user()->branch_id))
            {
                $totalRecords->whereIn('refill_orders.hospice_branch_id',$branch_id);
            }
            else
            {
                $totalRecords->where('refill_orders.hospice_branch_id',0);
            }

            
        }
        // for nurse
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
        {
            $totalRecords->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
            ->where('nurse_branches.user_id', Auth::user()->id);
        }
        $total = $totalRecords->first();
        $query = RefillOrderItems::select('refill_orders.*','refill_order_items.rx_number as rxNumber1','refill_order_items.drug_name as drug_name1' ,'refill_order_items.current_refill_date as current_refill_date1','refill_order_items.last_refill_date as last_refill_date1','refill_order_items.original_rx_date as original_rx_date1','refill_order_items.refill_left as refill_left1', 'refill_order_items.rx_id as rx_id1', 'rxs.rx_id as rx_newleaf_id')
        ->join('refill_orders', 'refill_orders.id', 'refill_order_items.refill_order_id', 'rxs.rx_id')
        ->leftJoin('rxs', 'rxs.rx_number', 'refill_order_items.rx_number')
        ->whereIn('refill_orders.status',[1,2]);
        
        // pharmacy admin or delivercare user
        if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
        {
            $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
            $query->whereIn('refill_orders.pharmacy_id',$pharmacy_id);
        }
        // hospice admin
        if(Auth::user()->user_type == 2 && Auth::user()->role_id == 1)
        {
            $hospice_id = Auth::user()->hospice_id;
            $query->where('refill_orders.hospice_id',$hospice_id);
        }
        // branch admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
        {
            $branch_id = explode(",",Auth::user()->branch_id);

            if(!empty(Auth::user()->branch_id))
            {
                $query->whereIn('refill_orders.hospice_branch_id',$branch_id);
            }
            else
            {
                $query->where('refill_orders.hospice_branch_id',0);
            }

            
        }
        // for nurse
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
        {
            $query->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
            ->where('nurse_branches.user_id', Auth::user()->id);
        }

        $filteredq = RefillOrderItems::selectRaw('count(*) as total')->join('refill_orders', 'refill_orders.id', 'refill_order_items.refill_order_id')->whereIn('status',[1,2]);
        
        // pharmacy admin or delivercare user
        if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
        {
            $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
            $filteredq->whereIn('refill_orders.pharmacy_id',$pharmacy_id);
        }
        // hospice admin
        if(Auth::user()->user_type == 2 && Auth::user()->role_id == 1)
        {
            $hospice_id = Auth::user()->hospice_id;
            $filteredq->where('refill_orders.hospice_id',$hospice_id);
        }
        // branch admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
        {
            $branch_id = explode(",",Auth::user()->branch_id);

            if(!empty(Auth::user()->branch_id))
            {
                $filteredq->whereIn('refill_orders.hospice_branch_id',$branch_id);
            }
            else
            {
                $filteredq->where('refill_orders.hospice_branch_id',0);
            }

            
        }
        // for nurse
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
        {
            $filteredq->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
            ->where('nurse_branches.user_id', Auth::user()->id);
        }
        
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where('patient_name', 'like', '%' . $search . '%')
                    ->orWhere('refill_order_items.drug_name', 'like', '%' . $search . '%')
                    ->orWhere('order_number', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where('patient_name', 'like', '%' . $search . '%')
                    ->orWhere('refill_order_items.drug_name', 'like', '%' . $search . '%')
                    ->orWhere('order_number', 'like', '%' . $search . '%');
            });
            $totalResult = $filteredq->first();
            $totalfiltered = $totalResult->total;
        }
        
        if($req['branch_id'] != '')
        {
            $query->where('hospice_branch_id', $req['branch_id']);
            $filteredq->where('hospice_branch_id', $req['branch_id']);
            $totalResult = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $totalResult->total;
        }
        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();
         
        $data = [];
            foreach ($query as $key => $value) 
            {
            
            $url = "rx/detail/" . $value->rx_newleaf_id;
            $rxNumber = "<a target='_blank' href='".$url."'>" . $value->rxNumber1 . "</a>";

            //last refill date
            $date1 = Carbon::parse($value->last_refill_date1)->format('m/d/Y');
            $date2 = getFormatedDate($date1, 'm/d/Y');
            if(empty($date2))
            {
                $date2 = '-';
            }elseif($date2 === "01/01/1970")
            {
                $date2 = '-';
            }else
            {
                $date2;
            }

            //original rx date
            $originalDate = Carbon::parse($value->original_rx_date1)->format('m/d/Y');
            $originalRxDate = getFormatedDate($originalDate, 'm/d/Y');
            if(empty($originalRxDate))
            {
                $originalRxDate = '-';
            }elseif($originalRxDate === "01/01/1970")
            {
                $originalRxDate = '-';
            }else
            {
                $originalRxDate;
            }

            if ($value->signature_required == 1) {
                $signature_required = 'Yes';
            }else {
                $signature_required = 'No';
            }
            if ($value->refilled_placed_online == 'Y') {
                $refilled_placed_online = 'Yes';
            }else {
                $refilled_placed_online = 'No';
            }
            $data[] = [$value->patient_id, $value->patient_name, $value->order_number, $rxNumber, $value->drug_name1, getFormatedDate($value->current_refill_date1, 'm/d/Y'), $date2, $originalRxDate, $value->refill_left1,$value->shipping_method,$signature_required,$refilled_placed_online];
        }
        return array(
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }


}
