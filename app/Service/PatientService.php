<?php

namespace App\Service;

use App\Models\Hospice;
use App\Models\Facilities;
use App\Models\Branch;
use App\Models\Pharmacy;
use App\Models\Patients;
use App\Models\Drugs;
use App\Models\Rxs;
use App\Models\NurseBranch;
use App\Models\Cart;
use App\Models\Refill;
use App\Models\Shipping;
use App\Repository\ActivityRepository;
use App\Repository\AdminRepository;
use App\Repository\HospiceRepository;
use App\Repository\PatientRepository;
use App\Repository\UserRepository;
use App\Service\SyncService;
use Hash;
use DB;
use Auth;
use Response;

class PatientService
{

    protected $patientRepo, $activityRepo;

    /**
     * @param PatientRepository $patientRepo reference to patientRepo
     * @param ActivityRepository $activityRepo reference to activityRepo
     * 
     */
    public function __construct(PatientRepository $patientRepo, ActivityRepository $activityRepo, SyncService $syncService)
    {
        $this->patientRepo = $patientRepo;
        $this->activityRepo = $activityRepo;
        $this->syncService = $syncService;
    }

    /** 
     * Add patient information
     * @param array $patientData
     */
    public function addInformation($patientData)
    {
        try {
            $this->patientRepo->create($patientData);
            return 'success';
        } catch (\Exception $e) {
            return 'error';
        }

        // Save activity for new hospice added
        /* $activityData['module_name'] = config('app.activityModules')["Hospice"];
            $activityData['performed_by'] = Auth::user()->id;
            $activityData['description'] = str_replace('{PARAM}',$data['name'], config('app.activityDescriptions')['Added']);
            $this->activityRepo->create($activityData); */
    }

    /** 
     * Fetch hospice information
     * @param $id
     */
    public function fetchInformation($id)
    {
        return $this->patientRepo->fetch($id);
    }

    /** 
     * Add hospice information
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
        $orderby = ['id', 'first_name', 'address_1', 'city', 'state','shipping_method', 'facility_code', 'is_active', 'patient_status', 'created_at'];
        
        /*
        $total = Patients::selectRaw('count(*) as total')->whereNull('deleted_at')->where('facility_code', '>' , 0)->first();
        $query = Patients::selectRaw('patients.*,patients.state as stateName,patients.city as cityName')->where('facility_code', '>' , 0);
        $filteredq = Patients::whereNotNull('is_active')->where('facility_code', '>' , 0); */

	    $total = Patients::selectRaw('count(*) as total')->whereNull('deleted_at')->first();
        $query = Patients::leftJoin('branch', 'branch.id', '=', 'patients.facility_code')->leftJoin('facilities', 'facilities.id', 'branch.facility_id')
            ->selectRaw('patients.*,patients.state as stateName,patients.city as cityName');
        $filteredq = Patients::leftJoin('branch', 'branch.id', '=', 'patients.facility_code')->leftJoin('facilities', 'facilities.id', 'branch.facility_id')
            ->whereNotNull('is_active');
        
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->Where(DB::raw("CONCAT(first_name,' ',last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('facility_code', 'like', '%' . $search . '%')
                    ->orWhere('patients.city', 'like', '%' . $search . '%')
                    ->orWhere('patients.state', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->Where(DB::raw("CONCAT(first_name,' ',last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('facility_code', 'like', '%' . $search . '%')
                    ->orWhere('patients.city', 'like', '%' . $search . '%')
                    ->orWhere('patients.state', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();
        $data = [];

        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'patients_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'patients_delete');
        foreach ($query as $key => $value) {

            $action = '';
            $editUrl = route('show-edit-patients-form', $value->id);
            $id = 'PT'. $value->id;

            $isEdit = $isEditable ? '<a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>' : '';
            $isDelete = $isDeletable ? '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>' : '';

            $actionInner = '';
            if ($isEdit || $isDelete)
                $actionInner = '<div class="dropdown-menu dropdown-menu-right">' . $isEdit . $isDelete . '</div';

            $action = '<div class="dropdown">
          <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>' . $actionInner . '</div>';

          $status = $value->is_active == 1 ? 'Active' : 'Inactive';
            $statusClass = $value->is_active == 1 ? 'success' : 'danger';
            $statusHtml = '<i class="bx bxs-circle ' . $statusClass . ' font-small-1 mr-50"></i>
        <span>' . $status . '</span>';

            if ($value['patient_status'] == 1) {
                $patient_status = 'Active';
            } else if ($value['patient_status'] == 2) {
                $patient_status = 'InFacility';
            } else if ($value['patient_status'] == 3) {
                $patient_status = 'Transfer';
            }else {
                $patient_status = 'Inactive';
            }

            $facility_count = Facilities::where('hospice_id', $value->id)->count();
            $facilityUrl = route('facilities-list');
            $facility_link = '<a href=' . $facilityUrl . '?hospice_id=' . encrypt($value->id) . '>' . $facility_count . '</a>';

            $facilityBranch  = 'FB'. $value->facility_code;
            if(isset(config('app.patient_shipping_method')[$value->shipping_method]))
            {
                $shippingMethod = config('app.patient_shipping_method')[$value->shipping_method]; 
            }
            else
            {
                $shippingMethod = config('app.patient_shipping_method')[0];
            }
            
            if($value->newleaf_customer_id)
            {
                $viewNurseListRoute = route('patientsList',$value->newleaf_customer_id);
                $patientName = '';
                $patientName .=' <a href=' . $viewNurseListRoute . '>' . $value->first_name . ' ' . $value->last_name . '</a>';
            }

            $data[] = [$id, $patientName, $value->address_1 . ' ' . $value->address_2, $value->cityName, $value->stateName, $facilityBranch,$shippingMethod , $statusHtml, $patient_status, getFormatedDate($value->created_at, 'm/d/Y'), $action];
        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

      public function updateInformation($request, $id)
    {
        try {
            $response = $this->patientRepo->update($request, $id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /** 
     * Delete patients
     * @param object $request
     */
    public function delete($request)
    {
        try {
            // Delete patients
            $this->patientRepo->delete($request->id);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /** 
     * Fetch nurse patient information
     * @param object $request
     */
    public function fetchListingPatients($request)
    {
        $req = $request->all();
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $branch_id = $req['branch_id'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        $orderby = ['last_name', 'id', 'patient_name', ''];

        $total = Patients::selectRaw('count(*) as total')->where('is_active',1)->whereNull('deleted_at')->where('facility_code', '>' , 0)->first();

        $query = Patients::selectRaw('patients.*')->where('is_active',1)->whereNull('deleted_at')->where('facility_code', '>' , 0);

        $filteredq = Patients::selectRaw('count(*) as total')->where('is_active',1)->whereNull('deleted_at')->where('facility_code', '>' , 0);

        $totalfiltered = $total->total;

        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(first_name,' ',last_name)"), 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }
         if ($branch_id != '') {
            $query->where('facility_code',$branch_id);
            $filteredq->where('facility_code',$branch_id);
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();
        $data = [];
        foreach ($query as $key => $value) {
            $viewNurseListRoute = route('patientsList',$value->newleaf_customer_id);
            $action = '';
            $action .=' <a href=' . $viewNurseListRoute . '><i class="bx bx-show primary"></i></a>';

            // name
            $name = $value->first_name;
            if(!empty($value->last_name))
            {
                $name .= " " . $value->last_name;
            }

            $data[] = [$name, $action];
        }
        return array( 
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }



     /** 
     * Fetch nurse patient prescriptions
     * @param object $request
     */


     public function fetchPatientsData($request)
     {
         $req = $request->all();
         $p_id = $req['id'];
         $start = $req['start'];
         $length = $req['length'];
         $search = $req['search']['value'];
         $order = $req['order'][0]['dir'];
         $column = $req['order'][0]['column'];
         $orderby = ['','rx_number','description','refills_remaining','date_filled','owed_quantity','original_days_supply','dosage_form','date_written','status'];

        $patientRxData = DB::table('patients')
         ->leftJoin('rxs','rxs.customer_id', '=', 'patients.newleaf_customer_id')
         ->leftJoin('drugs','drugs.newleaf_drug_id','=','rxs.prescribed_drug_id')
         ->where('rxs.customer_id', $p_id); 
         
          /*$patientRxData1 = Patients::selectRaw("patients.*, str_to_date(rxs.date_written, '%m/%d/%Y') as 'date_written2'")
         ->leftJoin('rxs','rxs.customer_id', '=', 'patients.newleaf_customer_id')
         ->leftJoin('drugs','drugs.newleaf_drug_id','=','rxs.prescribed_drug_id')
         ->where('rxs.customer_id', $p_id);*/
         
         
         
         
        // dd($patientRxData1);

       /* foreach($patientRxData as $key => $value){
                //dd($value->rx_id);
                $rx_id = $value->rx_id;
        }

         $rxDate = Refill::select('date_filled')->where('rx_id', $rx_id)
         ->orderBy('id', 'desc')
         ->limit(1)
         ->union($patientRxData)->get();*/



         if(!empty($patientRxData)){
             $pharmacy_id = $p_id;
         } else {
             $pharmacy_id = 0;
         }
         
         if ($search != '') {
             $patientRxData->where(function ($query2) use ($search) {
                 $query2->Where('rxs.rx_number', 'like', '%' . $search . '%')
                 ->orWhere('drugs.description', 'like', '%' . $search . '%');
             });
         }

         $total = $patientRxData->count(); //dd($total);
         $query = $patientRxData->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();

         
         // if cart master set then prepare cart
         $cart_master_id = $request->session()->get('cart_master_id');
         // delete all previous patient rxs because this is new patient
         $arrRxCarts = array();
         if (!empty($cart_master_id)) {
             // Fetch all cart items
             $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id', $cart_master_id)->get();
             foreach ($cartData as $key => $value) {
                 $arrRxCarts[$value->rxnumber] = $value->rxnumber;
             }
         }
         
         $data = [];
         $skippedRxs = 0;
         if (!empty($query)) {
             foreach ($query as $key => $value) { 
                 $checkbox = '';
                 
                 //url for rx number prescription
                 $urlPrescriptions = route('patient-prescriptions', $value->rx_id);
                 $rxPrescription = "<a href=" . $urlPrescriptions . " target='_blank'>".$value->rx_number."</a>";
                 //$rxPrescription = $value->RxNumber;
                 $refillsRemaining = floor($value->refills_remaining);
                 $qtyDispensed = floor($value->verified_quantity_dispensed);
                 $OriginalDaysSupply = floor($value->original_days_supply);
                 $qtyRemaining = floor($value->original_quantity - $value->owed_quantity);
                 $currentDate = date("Y-m-d");
                 $prescriptionDate = getFormatedDate($value->date_expires, 'Y-m-d');
                 //$dateFilled = getFormatedDate($value->DateFilled, 'Y-m-d');
                 $drugDirectSource = config('app.drug_direct_source');
                 $status = $value->status;
                 // Use strtotime() function to convert
                 // date into dateTimestamp
                 $dateTimestamp1 = strtotime($currentDate);
                 $dateTimestamp2 = strtotime($prescriptionDate);
                 // Compare the timestamp date
                 if ($dateTimestamp1 > $dateTimestamp2) {
                     // $checkbox = "<div class='checkbox checkbox-primary checkbox-glow'><input type='checkbox' id='checkboxGlow_".$value['RxNumber']."' value='".$value['RxNumber']."' disabled name='rxChkBox'><label for='checkboxGlow_".$value['RxNumber']."'></label></div>";
                     $flagRowStatus = "Expired";
                 } elseif ($refillsRemaining <= 0 || $value->Is_cancelled === true || $value->Is_cancelled == "True" || $status == 9) {
                     // $checkbox = "<div class='checkbox checkbox-primary checkbox-glow'><input type='checkbox' id='checkboxGlow_".$value['RxNumber']."' value='".$value['RxNumber']."' disabled name='rxChkBox'><label for='checkboxGlow_".$value['RxNumber']."'></label></div>";
                     $flagRowStatus = "Inactive";
                 } else {
                     // if this rx is already added in cart
                     if (isset($arrRxCarts[$value->rx_number])) {
                         $checkbox = "<div class='checkbox checkbox-primary checkbox-glow'><input type='checkbox' id='checkboxGlow_" . $value->rx_number . "' value='" . $value->rx_number . "' name='rxChkBox' checked='checked' disabled><label for='checkboxGlow_" . $value->rx_number . "'></label></div>";
                         $flagRowStatus = "Active";
                     } else {
                         $checkbox = "<div class='checkbox checkbox-primary checkbox-glow'><input type='checkbox' class='rxChkBoxClass' id='checkboxGlow_" . $value->rx_number . 
                         "' value='" . $value->rx_number . "' name='rxChkBox' onclick='checkCheckboxes()' ><label for='checkboxGlow_" . $value->rx_number . "'></label></div>";
                         $flagRowStatus = "Active";
                     }
                 }
                 // Fetch Drug Information
                 $drugId = $value->prescribed_drug_id;
                 $drugData = Drugs::selectRaw('drugs.*')->whereNull('deleted_at')->where('newleaf_drug_id', $drugId)->first();
                 $dosage_form = "-";
                 if (!empty($drugData->dosage_form)) {
                     $dosage_form = $drugData->dosage_form;
                 }
                 $drugName = $value->description;
                 if ($drugName == "N/A") {
                     $skippedRxs++;
                     continue;
                 }
                 
                 $drugDecs = "<span tabindex='0' data-toggle='popover' title='".$value->original_sig."' data-content='".$drugName."' style='cursor:pointer'>$drugName</span>";

                 $qtyRemaining = str_replace('-', '+', $qtyRemaining);
                 $rxDate = Refill::select('date_filled')->where('rx_id', $value->rx_id)
                 ->orderBy('id', 'desc')
                 ->limit(1)
                 ->first();
                 if ($rxDate['date_filled']) {
                     $date = strtotime($rxDate['date_filled']);
                     $rxDateFormat = date('m/d/Y', $date);
                 } else {
                     $rxDateFormat = "N/A";
                 }



                 
                 //$data [] = [$checkbox, $rxPrescription, $drugDecs, getFormatedDate($value->date_written, 'm/d/Y'), $rxDateFormat, $dosage_form, empty($OriginalDaysSupply) ? 0 : $OriginalDaysSupply, $refillsRemaining, $qtyRemaining, $flagRowStatus];
                 $data [] = [$checkbox, $rxPrescription, $drugDecs, $refillsRemaining, $rxDateFormat, $qtyRemaining, empty($OriginalDaysSupply) ? 0 : $OriginalDaysSupply, $dosage_form, getFormatedDate($value->date_written, 'm/d/Y'), $flagRowStatus];          
             }

             //dd($data);

             //$updatedData = $data;
         }
          
         return array(
             "recordsTotal" => intval($total - $skippedRxs),
             "recordsFiltered" => intval($total - $skippedRxs),
             "data" => $data,
         );
         
     }    
 
  
 

    
    /*
        Fetch Rx Details
    */
    public function fetchRxData($rxId)
    {
        // Get customer_id from Rxs
        $customer_newleaf = Rxs::select('rxs.customer_id')->where('rx_id',$rxId)->first();

        if(empty($customer_newleaf))
        {
            $customer_id = 0;
        }
        else
        {
            $customer_id = $customer_newleaf->customer_id;
        }

        // Get pharmacy_id
        $pharmacyData = Patients::select('pharmacy.id')
        ->where('patients.newleaf_customer_id',$customer_id)
        ->join('branch', 'branch.id', 'patients.facility_code')
        ->join('facilities', 'facilities.id', 'branch.facility_id')
        ->join('pharmacy', 'pharmacy.id', 'facilities.pharmacy_id')
        ->first();

        if(!empty($pharmacyData))
        {
            $pharmacy_id = $pharmacyData->id;
        }
        else
        {
            $pharmacy_id = 0;
        }

        // Fetch newLeaf data for rx
        $endpoint = "/Rx?";

        # Filter
        $filterSingle = "RxId eq ".$rxId; 

        $select = "RxId,RxNumber,CustomerId,PrescribedDrugId,DateWritten,DateExpires,VerifiedQuantityDispensed,RefillsAuthorized,RefillsRemaining,OriginalSig,OriginalDaysSupply,OriginalQuantity,OwedQuantity";

        // Set API Request parameters
        $api_request_parameters = array(
            '$filter' => $filterSingle,
            '$select' => $select,
        );

        // newLeaf data for patient rxs
        $patientRxs = array();

        // newleafLoad data
        $newleafDataLoad = true;

        // Fetch patient's rxs data
        $response = $this->syncService->fetchNewLeafData($pharmacy_id, $endpoint, $api_request_parameters);
        if($response && !empty($response['value'][0]))
        {
            $response = $response['value'][0];    
        }
        else
        {
            // newleaf down so fetch with data
            $response = Rxs::select('rx_id as RxId','rx_number as RxNumber','customer_id as CustomerId','prescribed_drug_id as PrescribedDrugId','date_written as DateWritten','date_expires as DateExpires','verified_quantity_dispensed as VerifiedQuantityDispensed','refills_authorized as RefillsAuthorized','refills_remaining as RefillsRemaining','original_days_supply as OriginalDaysSupply','original_quantity as OriginalQuantity','owed_quantity as OwedQuantity','original_sig as OriginalSIG','original_sig_expanded as OriginalSIGExpanded','Is_cancelled as IsCancelled')->where('rx_id',$rxId)->first();

            $newleafDataLoad = false;
        }
        
         // dd($response);

        // Fetch Drug Information
        $drugId = $response['PrescribedDrugId'];
        $drugData = Drugs::selectRaw('drugs.*')->whereNull('deleted_at')->where('newleaf_drug_id', $drugId)->first(); 
        $rxDate = Refill::select('date_filled')->where('rx_id',$rxId)
                ->orderBy('id', 'desc')
                ->limit(1)
                ->first();
        if($rxDate['date_filled'])
        {
            $date = strtotime($rxDate['date_filled']);
            $rxDateFormat = date('m/d/Y', $date);
        }else{
            $rxDateFormat = "N/A";
        }
      
        $dateWritten = getFormatedDate($response['DateWritten'], 'm/d/Y');
        $response['dateWritten'] = $dateWritten;
        $drugName = "N/A";
        $response['drugName'] = $drugName;
        if(!empty($drugData->description))
        {
            $drugName = $drugData->description;
            $response['drugName'] = $drugName;
        }

        $dosage_form = "N/A";
        $response['dosage_form'] = $dosage_form;
        if(!empty($drugData->dosage_form))
        {
           $dosage_form = $drugData->dosage_form; 
           $response['dosage_form'] = $dosage_form;
        }

        $ndc = "N/A";
        $response['ndc'] = $ndc;
        if(!empty($drugData->new_ndc))
        {
           $ndc = $drugData->new_ndc; 
           $response['ndc'] = $ndc;
        }

        $strength = "N/A";
        $response['strength'] = $strength;
        if(!empty($drugData->strength))
        {
           $strength = $drugData->strength; 
           $response['strength'] = $strength;
        }

        // qty remaining
        $response['qty_remaining'] = floor($response['OriginalQuantity']-$response['OwedQuantity']);
	    $response['qty_remaining'] = str_replace('-', '+', $response['qty_remaining']);

        //original qty
        $response['original_quantity'] = floor($response['OriginalQuantity']);

         //refills remaining
        $response['refills_remaining'] = floor($response['RefillsRemaining']);

        //owed quantity
        $response['owed_quantity'] = floor($response['OwedQuantity']);

        // refill taken
        $response['refill_taken'] = floor($response['RefillsAuthorized']);

        // last refill date
        $response['last_refill_date'] = $rxDateFormat;

        // refill taken
        $response['refill_taken'] = $response['RefillsAuthorized'];

        // Fetch patient name
        $patientData = Patients::selectRaw('first_name,last_name')->whereNull('deleted_at')->where('newleaf_customer_id',$response['CustomerId'])->first();

        $patientName = $patientData->first_name;
        if(!empty($patientData->last_name))
        {
            
            $patientName .= " " . $patientData->last_name;
        }
        $response['patientName'] = $patientName;
        $response['patientId'] = $patientData->id;
        $response['newleafDataLoad'] = $newleafDataLoad;

        return $response;
    }

    public function getPatientNameById($id)
    {
        $patientData = Patients::selectRaw('first_name,last_name')->whereNull('deleted_at')->where('id',$id)->first();

        $patientName = $patientData->first_name;
        if(!empty($patientData->last_name))
        {
            $patientName .= " " . $patientData->last_name;
        }

        return $patientName;
    }

    public function fetchListingFillHistory($request)
    {
        $req = $request->all(0);
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        $orderby = ['created_on','refill_number','date_filled','sig','dispensed_quantity','rph_user_name','refill_shipments.tracking_number'];

        if($column == 0)
        {
            $column = 1;
            $order = "desc";
        }

        $total = Refill::selectRaw('count(*) as total')
                ->where('rx_id', $req['id'])
                ->whereNull('deleted_at')->first();
        $query = Refill::selectRaw('refills.*,refill_shipments.tracking_number,shipping.tracking_url')
                ->where('rx_id', $req['id'])
                ->leftJoin('refill_shipments','refill_shipments.refill_id','refills.refill_id')
                ->leftJoin('shipping','refill_shipments.courier','shipping.name');
        $filteredq = Refill::leftJoin('refill_shipments','refill_shipments.refill_id','refills.refill_id')->where('rx_id', $req['id'])->whereNull('deleted_at');

        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where('refill_shipments.tracking_number', 'like', '%' . $search . '%')
                ->orWhere('rph_user_name', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                 $query2->where('refill_shipments.tracking_number', 'like', '%' . $search . '%')
                 ->orWhere('rph_user_name', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();
        $data = [];
        foreach ($query as $key => $value) {
            if(!empty($value->tracking_number))
            {
                if(!empty($value->tracking_url))
                {
                    $url = $value->tracking_url . $value->tracking_number;
                    $trackingNumber = "<a target='_blank' href='".$url."'>" . $value->tracking_number . "</a>";
                }
                else
                {
                    $trackingNumber = $value->tracking_number;
                }
                 
            }else{
                $trackingNumber = "N/A";
            }
            //$data[] = [$req['name'], $value->created_on, $value->refill_number, $value->date_filled, $value->sig, $value->dispensed_quantity, $value->rph_user_name, $trackingNumber];
            $data[] = [$value->refill_number,$value->created_on, $value->date_filled, $value->sig, $value->dispensed_quantity, $value->rph_user_name, $trackingNumber];

        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }
}
