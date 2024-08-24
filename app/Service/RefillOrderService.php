<?php

namespace App\Service;

use App\Models\Branch;
use App\Models\Facility;
use App\Models\Hospice;
use App\Models\NurseBranch;
use App\Models\Pharmacy;
use App\Models\RefillOrder;
use App\Models\Shipping;
use App\Models\User;
use App\Repository\AdminRepository;
use App\Repository\HospiceRepository;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use App\Service\PatientService;
use App\Repository\RefillOrderRepository;
use Hash;
use PDO;
use Str;
use Auth;
use PDF;
use Carbon\Carbon;
use Imagick;
use File;

class RefillOrderService
{

    protected $hospiceRepo, $userRepo, $activityRepo, $refillOrderRepo;


    /**
     * @param HospiceRepository $hospiceRepo reference to hospiceRepo
     *
     */
    public function __construct(HospiceRepository $hospiceRepo, UserRepository $userRepo, ActivityRepository $activityRepo, RefillOrderRepository $refillOrderRepo)
    {
        $this->hospiceRepo = $hospiceRepo;
        $this->userRepo = $userRepo;
        $this->activityRepo = $activityRepo;
        $this->refillOrderRepo = $refillOrderRepo;
    }


    
    /**
     * Add fetch information
     * @param object $request
     */

     public function fetchListing($request)
     {
         // Used fpr Orders->Latest orders
         $req = $request->all(); 
         $status = $req['status'];
         $startDate = $req['startDate'];
         $endDate = $req['endDate'];
         $start = $req['start'];
         $length = $req['length'];
         $search = $req['search']['value'];
         $order = $req['order'][0]['dir'];
         $column = $req['order'][0]['column'];
         //$orderby = ['patient_name', 'created_at', 'order_number','newleaf_order_number', 'status','patient shipping_method','shipping_name','address','shipping method','notes','signature_required', 'shipped_by', 'tracking_number', 'hospice_branch_id','action'];
         $orderby = ['patient_name', 'created_at', 'order_number', 'status','patient_shipping_method','address_1','action'];
         
         $total = RefillOrder::selectRaw('count(*) as total')->whereNull('deleted_at')->first();
         $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
         $filteredq = RefillOrder::selectRaw('count(*) as total')->whereNull('deleted_at');
         $totalfiltered = $total->total;
         // hospice admin
         if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
         {
             $hospice_id = Auth::user()->hospice_id;
             $total = RefillOrder::selectRaw('count(*) as total')->where('refill_orders.hospice_id',$hospice_id)->whereNull('deleted_at')->first();
             $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->where('refill_orders.hospice_id',$hospice_id)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
             $filteredq = RefillOrder::selectRaw('count(*) as total')->where('refill_orders.hospice_id',$hospice_id)->whereNull('deleted_at');
             $totalfiltered = $total->total;
         }
         // pharmacy admin or delivercare user
         if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
         {
             $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
             $total = RefillOrder::selectRaw('count(*) as total')->whereIn('refill_orders.pharmacy_id',$pharmacy_id)->whereNull('deleted_at')->first();
             $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->whereIn('refill_orders.pharmacy_id',$pharmacy_id)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
             $filteredq = RefillOrder::selectRaw('count(*) as total')->whereIn('refill_orders.pharmacy_id',$pharmacy_id)->whereNull('deleted_at');
             $totalfiltered = $total->total;
         }
         // branch admin
         if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
         {
             $branch_id = explode(",",Auth::user()->branch_id);
             if(!empty(Auth::user()->branch_id))
             {
                 $total = RefillOrder::selectRaw('count(*) as total')->whereIn('refill_orders.hospice_branch_id',$branch_id)->whereNull('deleted_at')->first();
                 $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->whereIn('refill_orders.hospice_branch_id',$branch_id)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
                 $filteredq = RefillOrder::selectRaw('count(*) as total')->whereIn('refill_orders.hospice_branch_id',$branch_id)->whereNull('deleted_at');
             }
             else
             {
                 $total = RefillOrder::selectRaw('count(*) as total')->where('refill_orders.hospice_branch_id',0)->whereNull('deleted_at')->first();
                 $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->where('refill_orders.hospice_branch_id',0)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
                 $filteredq = RefillOrder::selectRaw('count(*) as total')->where('refill_orders.hospice_branch_id',0)->whereNull('deleted_at');
             }
 
             $totalfiltered = $total->total;
         }
         // for nurse
         if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
         {
             $total = RefillOrder::selectRaw('count(*) as total')->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
             ->where('nurse_branches.user_id', Auth::user()->id)->whereNull('refill_orders.deleted_at')->whereNull('nurse_branches.deleted_at')->first();
             $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
             ->where('nurse_branches.user_id', Auth::user()->id)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at')->whereNull('nurse_branches.deleted_at');
             $filteredq = RefillOrder::selectRaw('count(*) as total')->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
             ->where('nurse_branches.user_id', Auth::user()->id)->whereNull('refill_orders.deleted_at')->whereNull('nurse_branches.deleted_at');
             $totalfiltered = $total->total;
         }
         if ($search != '') {
             $query->where(function ($query2) use ($search) {
                 $query2->where('patient_name', 'like', '%' . $search . '%')
                     ->orWhere('newleaf_order_number', 'like', '%' . $search . '%')
                     ->orWhere('order_number', 'like', '%' . $search . '%');
             });
             $filteredq->where(function ($query2) use ($search) {
                 $query2->where('patient_name', 'like', '%' . $search . '%')
                     ->orWhere('newleaf_order_number', 'like', '%' . $search . '%')
                     ->orWhere('order_number', 'like', '%' . $search . '%');
             });
             $filteredq = $filteredq->selectRaw('count(*) as total')->first();
             $totalfiltered = $filteredq->total;
         }
         if ($req['branch_id'] != '') {
             $query->where('hospice_branch_id', $req['branch_id']);
             $filteredq->where('hospice_branch_id', $req['branch_id']);
             $filteredq = $filteredq->selectRaw('count(*)as total')->first();
             $totalfiltered = $filteredq->total;
         }
         if ($req['startDate'] != "" || $req['endDate'] != "") {
             $startDate = date('Y-m-d', strtotime($req['startDate'])) . " 00:00:00";
             $endDate = date('Y-m-d', strtotime($req['endDate'])) . " 23:59:59";
             $query->whereBetween('refill_orders.created_at', [$startDate, $endDate]);
             $filteredq->whereBetween('refill_orders.created_at', [$startDate, $endDate]);
             $filteredq = $filteredq->selectRaw('count(*) as total')->first();
             $totalfiltered = $filteredq->total;
         }
         if ($status != '') {
             if ($status == 'Pending') {
                 $query->where('status', '1');
                 $filteredq->where('status', '1');
             } elseif ($status == 'In Progress') {
                 $query->where('status', '2');
                 $filteredq->where('status', '2');
             } elseif ($status == 'Shipped') {
                 $query->where('status', '3');
                 $filteredq->where('status', '3');
             }
         }
         $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();
         $data = [];
         foreach ($query as $key => $value) {
             if ($value['status'] == 1) {
                 $status = 'PENDING';
                 $statusClass = 'badge-light-danger';
             } else if ($value['status'] == 2) {
                 $status = 'IN PROGRESS';
                 $statusClass = 'badge-light-warning';
             } else if ($value['status'] == 3) {
                 $status = 'SHIPPED';
                 $statusClass = 'badge-light-success';
             } else {
                 $status = '';
             }
             $statusHtml = '<div class="badge badge-pill ' . $statusClass . '">' . $status . '</div>';
             if (!empty($value['hospice_id'])) {
                 $hospiceData = $this->hospiceRepo->fetch($value['hospice_id']);
                 if (!empty($hospiceData)) {
                     $hospiceName = $hospiceData->code;
                 } else {
                     $hospiceName = '';
                 }
             }
             $tracking_number = '';
             $logoHtml = '';
             if ($value['status'] == 3) {
                 $trackingUrl = Shipping::getTrackingUrl($value['shipped_by']).$value['tracking_number'];
                 $tracking_number = '<a href="'.$trackingUrl.'" target="_blank">'.$value['tracking_number'].'</a>'  ?? '-';
                 $shippingLogoVal = Shipping::getLogo($value['shipped_by']);
                 if ($shippingLogoVal) {
                     $logoHtml = '<img class="rounded-rectangle" src="' . $shippingLogoVal . '" alt="user" height="35">';
                 } else {
                     $logoHtml = '-';
                 }
             }
             $isDelete = '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>';
             $isUpdate =  '<a class="dropdown-item update-order" data-id=' . $value->id . ' data-status=' . $value['status'] . ' data-tracking=' . $value['tracking_number'] . ' data-shipped=' . $value['shipped_by'] . ' href="javascript:void(0);"><i class="bx bx-pencil mr-1"></i> Update Order Status</a>' ;
             $generatePdf = '<a target="_blank" class="dropdown-item " href="'.route('generateOrdersPDF',$value->id).'"><i class="bx bx-download mr-1"></i>Download PDF</a>';
             // We can't use this since it recreates the Order due to the function
             //$generatetiff = '<a target="_blank" class="dropdown-item " href="'.route('generateOrdersTIFF',$value->id).'"><i class="bx bx-download mr-1"></i>Download TIFF</a>'; 
             $actionInner = '';
             // We can't use this since it recreates the Order due to the function
             //$actionInner = '<div class="dropdown-menu dropdown-menu-right">'.$isUpdate.$generatePdf.$generatetiff.$isDelete.'</div';
             $actionInner = '<div class="dropdown-menu dropdown-menu-right">'.$isUpdate.$generatePdf.$isDelete.'</div';
             $action = '<div class="dropdown">
           <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>' . $actionInner . '</div>';
             //dd($value->patient_shipping_method);
             $patientName = $value['patient_name'];
             //$patientShippingMethod = config('app.patient_shipping_method')[$value->patient_shipping_method];
             $patientShippingMethod = !empty(config('app.patient_shipping_method')[$value->patient_shipping_method]) ? config('app.patient_shipping_method')[$value->patient_shipping_method] : "N/A";
             $sign_required = $value->signature_required == 1 ? 'Yes' : 'No';
             $shippingMethod = $value->shipping_method;
             $address = $value->address_1.' '.$value->city.' '.$value->state.' '.$value->zipcode;
             $action = (!Auth::user()->hospice_id)? $action :'';
             $orderNumber ='<a href="'.route('refillOrderItemsindex',$value['order_number']).'" target="_blank">'.$value['newleaf_order_number'].'</a>'  ?? '-';;
             //if hospice admin,branch admin,nurse then do not pass action
             if(Auth::user()->user_type != 1 || Auth::user()->user_type == 2)
             {
                 //$data[] = [$patientName, getFormatedDate($value['created_at'], 'm/d/Y'), $value['order_number'],$value['newleaf_order_number'], $statusHtml,$patientShippingMethod,$value['shipping_name'],$address,$shippingMethod,$value['notes'],$sign_required, $logoHtml ?? '', $tracking_number, $hospiceName ?? ''];
                 $data[] = [$patientName, getFormatedDate($value->created_at, 'm/d/Y'), $value['newleaf_order_number'], $statusHtml, $patientShippingMethod, $address];
                }else{
                 //$data[] = [$patientName, getFormatedDate($value['created_at'], 'm/d/Y'), $value['order_number'],$value['newleaf_order_number'], $statusHtml,$patientShippingMethod,$value['shipping_name'],$address,$shippingMethod,$value['notes'],$sign_required, $logoHtml ?? '', $tracking_number, $hospiceName ?? '',$action];
                 $data[] = [$patientName, getFormatedDate($value->created_at, 'm/d/Y'), $value['newleaf_order_number'], $statusHtml, $patientShippingMethod, $address, $action];
             }
         }
         return array(
             "recordsTotal" => intval($total->total),
             "recordsFiltered" => intval($totalfiltered),
             "data" => $data,
         );
     }
 

    /**
     * Add fetch information
     * @param object $request
     */
    public function fetchListingAll($request)
    {
        // Used for Orders->Refill Orders
        $req = $request->all();
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        //$orderby = ['patient_name', 'created_at', 'order_number', 'status','patient shipping_method','shipping_name','address','shipping_method','notes','signature_required', 'shipped_by', 'tracking_number', 'hospice_branch_id','action'];
        //$orderby = ['patient_name', 'created_at', 'status', 'order_number', 'newleaf_order_number', 'shipping_method', 'address_1','shipping_method','notes','signature_required', 'shipped_by', 'tracking_number', 'refill_orders.hospice_branch_id'];
        $orderby = ['patient_name', 'order_number', 'newleaf_order_number', 'created_at', 'status', 'shipping_method', 'address_1','shipping_method','notes','signature_required', 'shipped_by', 'tracking_number', 'refill_orders.hospice_branch_id'];

	    // For delivercare admin
        $total = RefillOrder::selectRaw('count(*) as total')->whereNull('deleted_at')->first();
        $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
        $filteredq = RefillOrder::selectRaw('count(*) as total')->whereNull('deleted_at');
        $totalfiltered = $total->total;
        // pharmacy admin or delivercare user
        if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
        {
            $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
            $total = RefillOrder::selectRaw('count(*) as total')->whereIn('refill_orders.pharmacy_id',$pharmacy_id)->whereNull('deleted_at')->first();
            $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->whereIn('refill_orders.pharmacy_id',$pharmacy_id)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
            $filteredq = RefillOrder::selectRaw('count(*) as total')->whereIn('refill_orders.pharmacy_id',$pharmacy_id)->whereNull('deleted_at');
            $totalfiltered = $total->total;
        }
        // hospice admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
        {
            $hospice_id = Auth::user()->hospice_id;
            $total = RefillOrder::selectRaw('count(*) as total')->where('refill_orders.hospice_id',$hospice_id)->whereNull('deleted_at')->first();
            $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->where('refill_orders.hospice_id',$hospice_id)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
            $filteredq = RefillOrder::selectRaw('count(*) as total')->where('refill_orders.hospice_id',$hospice_id)->whereNull('deleted_at');
            $totalfiltered = $total->total;
        }
        // branch admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
        {
            $branch_id = explode(",",Auth::user()->branch_id);

            if(!empty(Auth::user()->branch_id))
            {
                $total = RefillOrder::selectRaw('count(*) as total')->whereIn('refill_orders.hospice_branch_id',$branch_id)->whereNull('deleted_at')->first();
                $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->whereIn('refill_orders.hospice_branch_id',$branch_id)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
                $filteredq = RefillOrder::selectRaw('count(*) as total')->whereIn('refill_orders.hospice_branch_id',$branch_id)->whereNull('deleted_at');
            }
            else
            {
                $total = RefillOrder::selectRaw('count(*) as total')->where('refill_orders.hospice_branch_id',0)->whereNull('deleted_at')->first();
                $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->where('refill_orders.hospice_branch_id',0)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at');
                $filteredq = RefillOrder::selectRaw('count(*) as total')->where('refill_orders.hospice_branch_id',0)->whereNull('deleted_at');
            }


            $totalfiltered = $total->total;
        }
        // for nurse
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
        {
            $total = RefillOrder::selectRaw('count(*) as total')->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
            ->where('nurse_branches.user_id', Auth::user()->id)->whereNull('refill_orders.deleted_at')
            ->whereNull('nurse_branches.deleted_at')->first();
            $query = RefillOrder::select('refill_orders.*','patients.shipping_method as patient_shipping_method')->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
            ->where('nurse_branches.user_id', Auth::user()->id)->leftJoin('patients', 'patients.id', '=', 'refill_orders.patient_id')->whereNull('refill_orders.deleted_at')
            ->whereNull('nurse_branches.deleted_at');
            $filteredq = RefillOrder::selectRaw('count(*) as total')->join('nurse_branches', 'nurse_branches.branch_id', 'refill_orders.hospice_branch_id')
            ->where('nurse_branches.user_id', Auth::user()->id)->whereNull('refill_orders.deleted_at')
            ->whereNull('nurse_branches.deleted_at');
            $totalfiltered = $total->total;
        }
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where('patient_name', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_order_number', 'like', '%' . $search . '%')
                    ->orWhere('order_number', 'like', '%' . $search . '%')
		    ->orWhere('tracking_number', 'like', '%' . $search . '%')
                    ->orWhere('hospice_branch_id', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where('patient_name', 'like', '%' . $search . '%')
                    ->orWhere('newleaf_order_number', 'like', '%' . $search . '%')
                    ->orWhere('order_number', 'like', '%' . $search . '%')
		    ->orWhere('tracking_number', 'like', '%' . $search . '%')
                    ->orWhere('hospice_branch_id', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }
        if ($req['startDate'] != "" || $req['endDate'] != "") {
            $startDate = date('Y-m-d', strtotime($req['startDate'])) . " 00:00:00";
            $endDate = date('Y-m-d', strtotime($req['endDate'])) . " 23:59:59";
            $query->whereBetween('refill_orders.created_at', [$startDate, $endDate]);
            $filteredq->whereBetween('refill_orders.created_at', [$startDate, $endDate]);
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }
        if ($req['status'] != '') {
            if ($req['status'] == 'Pending') {
                $query->where('status', '1');
                $filteredq->where('status', '1');
            } elseif ($req['status'] == 'In Progress') {
                $query->where('status', '2');
                $filteredq->where('status', '2');
            } elseif ($req['status'] == 'Shipped') {
                $query->where('status', '3');
                $filteredq->where('status', '3');
            }
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        if ($req['branch_id'] != '') {
            $query->where('hospice_branch_id', $req['branch_id']);
            $filteredq->where('hospice_branch_id', $req['branch_id']);
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();

        //dd($query);
        $data = [];
        foreach ($query as $key => $value) {
            $orderNumber ='<a href="'.route('refillOrderItemsindex',$value['order_number']).'" target="_blank">'.$value['newleaf_order_number'].'</a>'  ?? '-';;
            
            if ($value['status'] == 1) {
                $status = 'PENDING';
                $statusClass = 'badge-light-danger';
            } else if ($value['status'] == 2) {
                $status = 'IN PROGRESS';
                $statusClass = 'badge-light-warning';
            } else if ($value['status'] == 3) {
                $status = 'SHIPPED';
                $statusClass = 'badge-light-success';
            } else {
                $status = '';
            }
            $statusHtml = '<div class="badge badge-pill ' . $statusClass . '">' . $status . '</div>';
            if (!empty($value['hospice_id'])) {
                $hospiceData = $this->hospiceRepo->fetch($value['hospice_id']);
                if (!empty($hospiceData)) {
                    $hospiceName = $hospiceData->code;
                } else {
                    $hospiceName = '';
                }
            }
            $tracking_number = '';
            $logoHtml = '';
            if ($value['status'] == 3) {
                $trackingUrl = Shipping::getTrackingUrl($value['shipped_by']).$value['tracking_number'];
                $tracking_number = '<a href="'.$trackingUrl.'" target="_blank">'.$value['tracking_number'].'</a>'  ?? '-';
                $shippingLogoVal = Shipping::getLogo($value['shipped_by']);
                if ($shippingLogoVal) {
                    $logoHtml = '<img class="rounded-rectangle" src="' . $shippingLogoVal . '" alt="user" height="35">';
                } else {
                    $logoHtml = '-';
                }
            }
            $isDelete = '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>' ;
            $isUpdate =  '<a class="dropdown-item update-order" data-id=' . $value->id . ' data-status=' . $value['status'] . ' data-tracking=' . $value['tracking_number'] . ' data-shipped=' . $value['shipped_by'] . ' href="javascript:void(0);"><i class="bx bx-pencil mr-1"></i> Update Order Status</a>' ;
            $generatePdf = '<a target="_blank" class="dropdown-item " href="'.route('generateOrdersPDF',$value->id).'"><i class="bx bx-download mr-1"></i>Download PDF</a>';
            //$generateTiff = '<a target="_blank" class="dropdown-item " href="'.route('generateOrdersTIFF',$value->id).'"><i class="bx bx-download mr-1"></i>Download Tiff</a>';
            $actionInner = '';
            //$actionInner = '<div class="dropdown-menu dropdown-menu-right">'.$isUpdate.$generatePdf.$generateTiff.$isDelete.'</div';
            $actionInner = '<div class="dropdown-menu dropdown-menu-right">'.$isUpdate.$generatePdf.$isDelete.'</div';

            $action = '<div class="dropdown">
          <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>' . $actionInner . '</div>';
            $patientName = $value['patient_name'];
            $patientShippingMethod = !empty(config('app.patient_shipping_method')[$value->patient_shipping_method]) ? config('app.patient_shipping_method')[$value->patient_shipping_method] : "N/A";
            $sign_required = $value->signature_required == 1 ? 'Yes' : 'No';
            $shippingMethod = $value->shipping_method;
            $address = $value->address_1.' '.$value->city.' '.$value->state.' '.$value->zipcode;
            $action = (!Auth::user()->hospice_id)?$action:'';

             //if hospice admin,branch admin,nurse then do not pass action
            if((Auth::user()->user_type == 2 && Auth::user()->role_id == 1) || (Auth::user()->user_type == 2 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 2 && Auth::user()->role_id == 3))
            {
                //$data[] = [$patientName, getFormatedDate($value->created_at, 'm/d/Y'), $value['order_number'],$value['newleaf_order_number'], $statusHtml,$patientShippingMethod,$value['shipping_name'],$address,$shippingMethod,$value['notes'],$sign_required, $logoHtml ?? '', $tracking_number, $hospiceName ?? ''];
                $data[] = [$patientName, $value['order_number'], $orderNumber, getFormatedDate($value->created_at, 'm/d/Y'),  $statusHtml,$patientShippingMethod,$value['shipping_name'],$address,$shippingMethod,$value['notes'],$sign_required, $logoHtml ?? '', $tracking_number, $hospiceName ?? ''];

            }else{
                //$data[] = [$patientName, getFormatedDate($value->created_at, 'm/d/Y'), $value['order_number'],$value['newleaf_order_number'], $statusHtml,$patientShippingMethod,$value['shipping_name'],$address,$shippingMethod,$value['notes'],$sign_required, $logoHtml ?? '', $tracking_number, $hospiceName ?? '',$action];                
                $data[] = [$patientName, $value['order_number'], $orderNumber, getFormatedDate($value->created_at, 'm/d/Y'), $statusHtml, $patientShippingMethod, $value['shipping_name'],$address,$shippingMethod,$value['notes'],$sign_required, $logoHtml ?? '', $tracking_number, $hospiceName ?? '',$action];
            }
        }
        return array(
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

    public function updateOrderStatus($request)
    {
        try {
        $id = $request->id;
        $order = RefillOrder::find($id);
        $order->status = $request->order_status;
        $order->tracking_number = $request->tracking_number;
        $order->shipped_by = $request->shipping_carrier;
        $order->save();
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }

    }

    /**
     * Delete refill orders
     * @param object $request
     */
    public function delete($request)
    {
        try {
            // Delete refill orders
            $this->refillOrderRepo->delete($request->id);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

     /**
     * Delete latest orders
     * @param object $request
     */
    public function deleteLatestOrders($request)
    {
        try {
            // Delete latest orders
            $this->refillOrderRepo->deleteLatestOrders($request->id);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }


}
