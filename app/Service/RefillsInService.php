<?php

namespace App\Service;

use App\Models\RefillOrder;
use App\Models\RefillOrderItems;
use App\Models\NurseBranch;
use App\Models\Branch;
use App\Models\RefillShipment;
use App\Models\Shipping;
use App\Models\NewLeafOrder;
use App\Models\Rxs;
use App\Repository\ActivityRepository;
use App\Repository\AdminRepository;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use App\Repository\RefillsInQueueRepository;
use Carbon\Carbon;
use Hash;
use DB;
use Auth;

class RefillsInService
{

    protected $refillsInRepository, $userRepo, $activityRepo;

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

    /**
     * Add hospice information
     * @param object $requestn
     */
    // public function fetchListing($request)
    // {
    //     $req = $request->all();
    //     $start = $req['start'];
    //     $length = $req['length'];
    //     $search = $req['search']['value'];
    //     $order = $req['order'][0]['dir'];
    //     $column = $req['order'][0]['column'];
    //     $orderby = ['refill_orders.patient_id', 'refill_orders.patient_name', 'refill_orders.order_number', 'refill_order_items.rx_number', 'refill_order_items.drug_name', 'current_refill_date', 'last_refill_date', 'original_rx_date', 'refill_left','shipping_method','signature_required','refilled_placed_online'];
    //     $totalRecords = RefillOrderItems::selectRaw('count(*) as total')
    //             ->join('refill_orders', 'refill_orders.id', 'refill_order_items.refill_order_id')
    //             ->whereIn('status',[1,2,3]);

    //     // pharmacy admin or delivercare user
    //     if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
    //     {
    //         $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
    //         $totalRecords->whereIn('refill_orders.pharmacy_id',$pharmacy_id);
    //     }
    //     // hospice admin
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
    //     {
    //         $hospice_id = Auth::user()->hospice_id;
    //         $totalRecords->where('refill_orders.hospice_id',$hospice_id);
    //     }
    //     // branch admin
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
    //     {
    //         $branch_id = explode(",",Auth::user()->branch_id);

    //         if(!empty(Auth::user()->branch_id))
    //         {
    //             $totalRecords->whereIn('refill_orders.hospice_branch_id',$branch_id);
    //         }
    //         else
    //         {
    //             $totalRecords->where('refill_orders.hospice_branch_id',0);
    //         }


    //     }
    //     // for nurse
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
    //     {
    //         $totalRecords->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
    //         ->where('nurse_branches.user_id', Auth::user()->id);
    //     }
    //     $total = $totalRecords->first();
    //     $query = RefillOrderItems::select('refill_orders.*','refill_order_items.rx_number as rxNumber1','refill_order_items.drug_name as drug_name1' ,'refill_order_items.current_refill_date as current_refill_date1','refill_order_items.last_refill_date as last_refill_date1','refill_order_items.original_rx_date as original_rx_date1','refill_order_items.refill_left as refill_left1', 'refill_order_items.rx_id as rx_id1', 'rxs.rx_id as rx_newleaf_id')
    //     ->join('refill_orders', 'refill_orders.id', 'refill_order_items.refill_order_id', 'rxs.rx_id')
    //     ->leftJoin('rxs', 'rxs.rx_number', 'refill_order_items.rx_number')
    //     ->whereIn('refill_orders.status',[1,2,3]);

    //     // pharmacy admin or delivercare user
    //     if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
    //     {
    //         $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
    //         $query->whereIn('refill_orders.pharmacy_id',$pharmacy_id);
    //     }
    //     // hospice admin
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
    //     {
    //         $hospice_id = Auth::user()->hospice_id;
    //         $query->where('refill_orders.hospice_id',$hospice_id);
    //     }
    //     // branch admin
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
    //     {
    //         $branch_id = explode(",",Auth::user()->branch_id);

    //         if(!empty(Auth::user()->branch_id))
    //         {
    //             $query->whereIn('refill_orders.hospice_branch_id',$branch_id);
    //         }
    //         else
    //         {
    //             $query->where('refill_orders.hospice_branch_id',0);
    //         }


    //     }
    //     // for nurse
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
    //     {
    //         $query->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
    //         ->where('nurse_branches.user_id', Auth::user()->id);
    //     }

    //     $filteredq = RefillOrderItems::selectRaw('count(*) as total')->join('refill_orders', 'refill_orders.id', 'refill_order_items.refill_order_id')->whereIn('status',[1,2,3]);

    //     // pharmacy admin or delivercare user
    //     if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
    //     {
    //         $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
    //         $filteredq->whereIn('refill_orders.pharmacy_id',$pharmacy_id);
    //     }
    //     // hospice admin
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
    //     {
    //         $hospice_id = Auth::user()->hospice_id;
    //         $filteredq->where('refill_orders.hospice_id',$hospice_id);
    //     }
    //     // branch admin
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
    //     {
    //         $branch_id = explode(",",Auth::user()->branch_id);

    //         if(!empty(Auth::user()->branch_id))
    //         {
    //             $filteredq->whereIn('refill_orders.hospice_branch_id',$branch_id);
    //         }
    //         else
    //         {
    //             $filteredq->where('refill_orders.hospice_branch_id',0);
    //         }


    //     }
    //     // for nurse
    //     if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
    //     {
    //         $filteredq->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
    //         ->where('nurse_branches.user_id', Auth::user()->id);
    //     }

    //     $totalfiltered = $total->total;
    //     if ($search != '') {
    //         $query->where(function ($query2) use ($search) {
    //             $query2->where('patient_name', 'like', '%' . $search . '%')
    //                 ->orWhere('refill_order_items.drug_name', 'like', '%' . $search . '%')
    //                 ->orWhere('order_number', 'like', '%' . $search . '%');
    //         });
    //         $filteredq->where(function ($query2) use ($search) {
    //             $query2->where('patient_name', 'like', '%' . $search . '%')
    //                 ->orWhere('refill_order_items.drug_name', 'like', '%' . $search . '%')
    //                 ->orWhere('order_number', 'like', '%' . $search . '%');
    //         });
    //         $totalResult = $filteredq->first();
    //         $totalfiltered = $totalResult->total;
    //     }

    //     if($req['branch_id'] != '')
    //     {
    //         $query->where('hospice_branch_id', $req['branch_id']);
    //         $filteredq->where('hospice_branch_id', $req['branch_id']);
    //         $totalResult = $filteredq->selectRaw('count(*) as total')->first();
    //         $totalfiltered = $totalResult->total;
    //     }
    //     $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();

    //     $data = [];
    //         foreach ($query as $key => $value)
    //         {

    //         $url = "rx/detail/" . $value->rx_newleaf_id;
    //         $rxNumber = "<a target='_blank' href='".$url."'>" . $value->rxNumber1 . "</a>";

    //         $date1 = Carbon::parse($value->last_refill_date1)->format('m/d/Y');
    //         $date2 = getFormatedDate($date1, 'm/d/Y');
    //         if(empty($date2))
    //         {
    //             $date2 = '-';
    //         }elseif($date2 === "01/01/1970")
    //         {
    //             $date2 = '-';
    //         }else
    //         {
    //             $date2;
    //         }

    //         //original rx date
    //         $originalDate = Carbon::parse($value->original_rx_date1)->format('m/d/Y');
    //         $originalRxDate = getFormatedDate($originalDate, 'm/d/Y');
    //         if(empty($originalRxDate))
    //         {
    //             $originalRxDate = '-';
    //         }elseif($originalRxDate === "01/01/1970")
    //         {
    //             $originalRxDate = '-';
    //         }else
    //         {
    //             $originalRxDate;
    //         }

    //         if ($value->signature_required == 1) {
    //             $signature_required = 'Yes';
    //         }else {
    //             $signature_required = 'No';
    //         }
    //         if ($value->refilled_placed_online == 'Y') {
    //             $refilled_placed_online = 'Yes';
    //         }else {
    //             $refilled_placed_online = 'No';
    //         }
    //         $data[] = [$value->patient_id, $value->patient_name, $value->order_number, $rxNumber, $value->drug_name1, getFormatedDate($value->current_refill_date1, 'm/d/Y'), $date2, $originalRxDate, $value->refill_left1,$value->shipping_method,$signature_required,$refilled_placed_online];
    //     }
    //     return array(
    //         "recordsTotal" => intval($total->total),
    //         "recordsFiltered" => intval($totalfiltered),
    //         "data" => $data,
    //     );
    // }

    public function fetchListing($request)
    {
        $req = $request->all();
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        //$orderby = ['order_date','patients.first_name','newleaf_orders.order_number','newleaf_orders.order_date','newleaf_orders.tracking_number','newleaf_orders.courier_name','branch.code'];
       //$orderby = ['newleaf_orders.order_number','patients.first_name','order_date','newleaf_orders.order_date','newleaf_orders.tracking_number','newleaf_orders.courier_name','branch.code'];
        $orderby = ['first_name','order_number','order_date2','tracking_number','courier_name','branchCode'];

        /* Do not use!
        if($column == 2)
        {
            $order = "desc";
        }*/

        $total = NewLeafOrder::selectRaw('count(*) as total');

        //pharmacy admin or delivercare user
        if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
        {
            $pharmacy_id = explode(",",Auth::user()->pharmacy_id);

            $total->join('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')->join('branch', 'branch.id', 'patients.facility_code')->join('pharmacy', 'branch.pharmacy_newleaf_id', 'pharmacy.pharmacy_newleaf_id')
                ->whereIn('pharmacy.id',$pharmacy_id);
        }

        // hospice admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
        {
            $hospice_id = Auth::user()->hospice_id;

            // Get all branches for this hospice
            $branchData = Branch::select('id')->where('hospice_id', $hospice_id)->whereNull('deleted_at')->get();

            if(!empty($branchData))
            {
                $branch_ids = "";
                foreach ($branchData as $key => $value) {
                    if(empty($branch_ids))
                    {
                        $branch_ids = $value->id;
                    }
                    else
                    {
                        $branch_ids = "," . $value->id;
                    }
                }

                $branch_id = explode(",",$branch_ids);

                $total->join('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')
                ->whereIn('patients.facility_code',$branch_id);
            }
            else
            {
                $total->join('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')
                ->where('patients.facility_code',0);
            }
        }

        // branch admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
        {
            $branch_id = explode(",",Auth::user()->branch_id);

            if(!empty(Auth::user()->branch_id))
            {
                $total->join('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')
                ->whereIn('patients.facility_code',$branch_id);
            }
            else
            {
                $total->join('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')
                ->where('patients.facility_code',0);
            }
        }

        // for nurse
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
        {

            $total->join('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')->join('nurse_branches', 'nurse_branches.branch_id', 'patients.facility_code')
                ->where('nurse_branches.user_id', Auth::user()->id);
        }


        /*$query = NewLeafOrder::selectRaw('newleaf_orders.*,patients.first_name as firstName, patients.last_name as lastName,patients.facility_code,branch.code as branchCode')
            ->leftJoin('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')
            ->leftJoin('branch', 'branch.id', 'patients.facility_code');*/

        $query = NewLeafOrder::selectRaw("newleaf_orders.id,newleaf_orders.pharmacy_id,newleaf_orders.patient_id,convert(order_number, int) as 'order_number',
        newleaf_orders.tracking_number,newleaf_orders.courier_name,newleaf_orders.shipped_by, str_to_date(order_date, '%m/%d/%Y') as 'order_date2',
        patients.first_name as firstName, patients.last_name as lastName,patients.facility_code,branch.code as branchCode")
        ->leftJoin('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')
        ->leftJoin('branch', 'branch.id', 'patients.facility_code');

        
        //pharmacy admin or delivercare user
        if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
        {
            $pharmacy_id = explode(",",Auth::user()->pharmacy_id);

            $query->join('pharmacy', 'branch.pharmacy_newleaf_id', 'pharmacy.pharmacy_newleaf_id')
                ->whereIn('pharmacy.id',$pharmacy_id);
        }

        // hospice admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
        {
            $hospice_id = Auth::user()->hospice_id;

            // Get all branches for this hospice
            $branchData = Branch::select('id')->where('hospice_id', $hospice_id)->whereNull('deleted_at')->get();

            if(!empty($branchData))
            {
                $branch_ids = "";
                foreach ($branchData as $key => $value) {
                    if(empty($branch_ids))
                    {
                        $branch_ids = $value->id;
                    }
                    else
                    {
                        $branch_ids = "," . $value->id;
                    }
                }

                $branch_id = explode(",",$branch_ids);

                $query->whereIn('patients.facility_code',$branch_id);
            }
            else
            {
                $query->where('patients.facility_code',0);
            }
        }

        // branch admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
        {
            $branch_id = explode(",",Auth::user()->branch_id);

            if(!empty(Auth::user()->branch_id))
            {
                $query->whereIn('patients.facility_code',$branch_id);
            }
            else
            {
                $query->where('patients.facility_code',0);
            }
        }

        // for nurse
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
        {

            $query->join('nurse_branches', 'nurse_branches.branch_id', 'patients.facility_code')
                ->where('nurse_branches.user_id', Auth::user()->id);
        }


        $filteredq = NewLeafOrder::selectRaw("newleaf_orders.id,newleaf_orders.pharmacy_id,newleaf_orders.patient_id,convert(order_number, int) as 'order_number',
        newleaf_orders.tracking_number,newleaf_orders.courier_name,newleaf_orders.shipped_by, str_to_date(order_date, '%m/%d/%Y') as 'order_date',
        patients.first_name as firstName, patients.last_name as lastName,patients.facility_code,branch.code as branchCode")
        ->leftJoin('patients', 'patients.newleaf_customer_id', 'newleaf_orders.patient_id')
        ->leftJoin('branch', 'branch.id', 'patients.facility_code');

        //pharmacy admin or delivercare user
        if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
        {
            $pharmacy_id = explode(",",Auth::user()->pharmacy_id);

            $filteredq->join('pharmacy', 'branch.pharmacy_newleaf_id', 'pharmacy.pharmacy_newleaf_id')
                ->whereIn('pharmacy.id',$pharmacy_id);
        }

        // hospice admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
        {
            $hospice_id = Auth::user()->hospice_id;

            // Get all branches for this hospice
            $branchData = Branch::select('id')->where('hospice_id', $hospice_id)->whereNull('deleted_at')->get();

            if(!empty($branchData))
            {
                $branch_ids = "";
                foreach ($branchData as $key => $value) {
                    if(empty($branch_ids))
                    {
                        $branch_ids = $value->id;
                    }
                    else
                    {
                        $branch_ids = "," . $value->id;
                    }
                }

                $branch_id = explode(",",$branch_ids);

                $filteredq->whereIn('patients.facility_code',$branch_id);
            }
            else
            {
                $filteredq->where('patients.facility_code',0);
            }
        }

        // branch admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
        {
            $branch_id = explode(",",Auth::user()->branch_id);

            if(!empty(Auth::user()->branch_id))
            {
                $filteredq->whereIn('patients.facility_code',$branch_id);
            }
            else
            {
                $filteredq->where('patients.facility_code',0);
            }
        }

        // for nurse
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
        {

            $filteredq->join('nurse_branches', 'nurse_branches.branch_id', 'patients.facility_code')
                ->where('nurse_branches.user_id', Auth::user()->id);
        }
        
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(patients.first_name,' ',patients.last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.courier_name', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.order_date', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.tracking_number', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.courier_name', 'like', '%' . $search . '%')
                    ->orWhere('branch.code', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(patients.first_name,' ',patients.last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.courier_name', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.order_date', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.tracking_number', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_orders.courier_name', 'like', '%' . $search . '%')
            ->orWhere('branch.code', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        } 

        $totalRecs = $total->first(); 
        $totalfiltered = $totalRecs->total;

        $query = $query->groupBy('newleaf_orders.order_number')->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();

        $data = [];
        foreach ($query as $key => $value) {
            $tracking_number = '';
            $order_number = '';
                if ($value['tracking_number'] != '') {
                        $trackingUrl = Shipping::getTrackingUrl($value['shipped_by']).$value['tracking_number'];
                        $tracking_number = '<a href="'.$trackingUrl.'" target="_blank">'.$value['tracking_number'].'</a>'  ?? '-';
            }

            $orderNumber ='<a href="'.route('refillOrderItemsindex',$value['order_number']).'" target="_blank">'.$value['order_number'].'</a>'  ?? '-';;

            $data[] = [$value->firstName . ' ' . $value->lastName, $orderNumber, getFormatedDate($value->order_date2, 'm/d/Y'), $tracking_number,$value->courier_name,$value->branchCode];
        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($totalRecs->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

    public function fetchItemListing($request)
    {
        $req = $request->all(); 
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        //$orderby = ['patients.first_name','refills.refill_number','refill_shipments.newleaf_order_number','rxs.rx_number','drugs.description','',''];
        $orderby = ['refill_number','newleaf_order_number','rx_number','description','dateRefilled','rxDate'];
        $orderNumber = $req['id'];

        $total = RefillShipment::selectRaw('count(*) as total')->where('newleaf_order_number',$orderNumber)->first();
        $query = RefillShipment::select('patients.first_name AS PFname','patients.last_name AS PLname','refills.refill_number AS refillNumber','refill_shipments.newleaf_order_number AS orderNumber','rxs.rx_number AS rxNumber','rxs.rx_id AS rxNewLeafId','drugs.description AS drugDescription','refills.date_filled AS dateRefilled','rxs.date_written AS rxDate')->where('newleaf_order_number',$orderNumber)
            ->leftJoin('refills', 'refills.refill_id', 'refill_shipments.refill_id')
            ->leftJoin('rxs', 'rxs.rx_id', 'refills.rx_id')
            ->leftJoin('drugs', 'drugs.newleaf_drug_id', 'refills.drug_id')
            ->leftJoin('patients', 'patients.newleaf_customer_id', 'rxs.customer_id');
        $filteredq = RefillShipment::where('newleaf_order_number',$orderNumber)
            ->leftJoin('refills', 'refills.refill_id', 'refill_shipments.refill_id')
            ->leftJoin('rxs', 'rxs.rx_id', 'refills.rx_id')
            ->leftJoin('drugs', 'drugs.newleaf_drug_id', 'refills.drug_id')
            ->leftJoin('patients', 'patients.newleaf_customer_id', 'rxs.customer_id');

        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(patients.first_name,' ',patients.last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('rxs.rx_number', 'like', '%' . $search . '%')
                    ->orWhere('drugs.description', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(patients.first_name,' ',patients.last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('rxs.rx_number', 'like', '%' . $search . '%')
                    ->orWhere('drugs.description', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();

        $data = [];
        foreach ($query as $key => $value) {
            $pateintName = '';
            if (!empty($value->PFname))
            {
                $pateintName = $value->PFname;
                if (!empty($value->LFname))
                {
                    $pateintName .= ' '.$value->LFname;
                }
            }
            $rxNumber = '';
            $rxNumber = '<a href="'.route('patient-prescriptions',$value->rxNewLeafId).'" target="_blank">'.$value->rxNumber.'</a>';
            //$data[] = [$pateintName,$value->refillNumber,$value->orderNumber,$rxNumber,$value->drugDescription,getFormatedDate($value->dateRefilled),getFormatedDate($value->rxDate)];
            $data[] = [$value->refillNumber,$value->orderNumber,$rxNumber,$value->drugDescription,getFormatedDate($value->dateRefilled,  'm/d/Y'),getFormatedDate($value->rxDate,  'm/d/Y')];

        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }
}
