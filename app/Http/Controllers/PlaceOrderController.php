<?php

namespace App\Http\Controllers;

use App\Models\CareKit;
use App\Models\CareKitItems;
use App\Models\EmailTemplates;
use App\Models\Facility;
use App\Models\Hospice;
use App\Models\User;
use App\Models\NurseBranch;
use App\Models\Pharmacy;
use App\Exports\OrdersExport;
use App\Models\Drugs;
use App\Models\OfflineOrder;
use App\Models\OfflineOrderItems;
use App\Models\Patients;
use App\Models\Prescriber;
use App\Models\PrescriberAddresses;
use App\Models\Rxs;
use App\Models\Rover;
use App\Models\Uds;
use App\Repository\EmailTemplatesRepository;
use App\Service\FacilityService;
use App\Service\HospiceService;
use App\Service\RoleService;
use App\Service\UserService;
use App\Service\BranchService;
use App\Service\ActivityService;
use App\Service\RefillOrderService;
use App\Service\OfflineOrderService;
use Illuminate\Http\Request;
use Auth;
use Spatie\PdfToImage\Pdf as Spdf;
use Session;
use Response;
use Excel;
use DB;
use PDF;
use Carbon\Carbon;
use Imagick;
use ImagickPixel;
use File;


class PlaceOrderController extends Controller

{

    protected $userService;
    protected $roleService;
    protected $facilityService;
    protected $hospiceService;
    protected $branchService;
    protected $activityService;
    protected $refillOrderService;
    protected $offlineOrderService;

    // /**
    //  * constructor for initialize Admin service
    //  *
    //  * @param HospiceService $hospiceService reference to hospiceService
    //  *
    //  */
    private $activityServie;

    public function __construct(UserService $userService, RoleService $roleService, FacilityService $facilityService, HospiceService $hospiceService, BranchService $branchService, ActivityService $activityService, RefillOrderService $refillOrderService, OfflineOrderService $offlineOrderService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->facilityService = $facilityService;
        $this->hospiceService = $hospiceService;
        $this->branchService = $branchService;
        $this->activityService = $activityService;
        $this->refillOrderService = $refillOrderService;
        $this->offlineOrderService = $offlineOrderService;
    }


    /**
     * Listing of the nurse
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $hospiceId = '';
        //$roverQText = '';
        $shippingMethodArr = config('app.shipping_methods');
        $roverService = config('app.rover_service'); //dd($roverService);
        $carekit = CareKit::select('hospice_care_kit_id', 'name')->where('is_active', 1)->get();
        $userData = $this->userService->fetchInformation(Auth::user()->id);
        if (!empty($userData['first_name']) && $userData['last_name']) {
            $rphVal = $userData['first_name'] . ' ' . $userData['last_name'] . ' (' . ucwords(substr($userData['first_name'], 0, 1) . '' . substr($userData['last_name'], 0, 1)) . ')';
        } elseif (!empty($userData['name'])) {
            $rphVal = $userData['name'];
        } else {
            $rphVal = "N/A";
        }

        $arrVal = [];
        if ($hospiceId) {
            $branch = $this->branchService->getDropDownListBranchAndHospice($hospiceId);
        } else {
            $branch = $this->branchService->getDropDownListBranchAndHospice();
        }
        return view('admin.orders.place-order', compact('branch', 'shippingMethodArr', 'rphVal', 'carekit', 'roverService'));
        $radioDefault = 
        '@foreach ($shippingMethodArr as $key => $val)
        <li class="d-inline-block mr-2 mb-1">
            <fieldset>
                <div class="radio radio-shadow">@if ($key != "rover" && $key != "uds_saturday" && $key != "uds_monday_friday" && $key != "ups")<input type="radio" value={{ $key }} id={{ $key }} name="shipping_method"
                        @if ($key == "FD2") checked @endif>
                    <label for={{ $key }}>{{ $val }}</label>
                    @endif
                </div>
            </fieldset>
        </li>
        @endforeach';

        return view('admin.orders.place-order', compact('branch', 'shippingMethodArr', 'rphVal', 'radioDefault', 't', 'roverService'));
    }

    /**
     * Listing of the nurse
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $result = $this->refillOrderService->fetchListing($request);
        return Response::json($result);
    }

    public function exportAll(Request $request)
    {
        $fileName = '';
        $time = str_replace(' ', '', Carbon::now()->format('d m Y H:i:s'));
        $fileName = 'DeliverCareX._All_Orders' . '_' . $time . '.csv';
        try {
            $val = Excel::download(new OrdersExport(), $fileName);
            if ($val) {
                $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
                $valueForAddOperation = [Auth::user()->name, Auth::user()->email];
                $this->activityService->logs('export', config('app.activityModules')["User"], '', config('app.activityModules')["User"], $keyForAddOperation, $valueForAddOperation);
            }
            return $val;
        } catch (\Exception $ex) {
            dd($ex);
        }
    }

    public function autoComplete(Request $request)
    {
        $input = $request->all();
        $prefix = $input['prefix'];
        $pharmacy_id = explode(",", Auth::user()->pharmacy_id);

        if ($prefix) {
            $result = array();
            preg_match('/^(.+?),(.+)$/', $prefix, $result);
            $lastShortname = $result[1] ?? "";
            $firstShortname = $result[2] ?? "";
            if ((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3)) {
                $pharmacy_id = explode(",", Auth::user()->pharmacy_id);
                $data = Patients::leftJoin('branch', 'branch.id', '=', 'patients.facility_code')->leftJoin('facilities', 'facilities.id', 'branch.facility_id')->orderby('first_name', 'asc')
                    ->select('patients.id', 'first_name', 'last_name', 'dob', 'city', 'state')
                    ->where('is_active', 1)
                    ->whereIn('facilities.pharmacy_id', $pharmacy_id)
                    ->whereNull('patients.deleted_at');
            } else {
                $data = Patients::orderby('first_name', 'asc')
                    ->select('patients.id', 'first_name', 'last_name', 'dob', 'city', 'state')
                    ->where('is_active', 1)
                    ->whereNull('patients.deleted_at');
            }
            if ($lastShortname && $firstShortname) {
                $data = $data->where(DB::raw('CONCAT(last_name)'), 'LIKE', $lastShortname . '%')
                    ->where(DB::raw('CONCAT(first_name)'), 'LIKE', $firstShortname . '%');
            } else {
                $data = $data->where(DB::raw('CONCAT(last_name)'), 'LIKE', $prefix . '%');
            }
            $data = $data->get();
        } else {
            $data = Patients::orderby('first_name', 'asc')->select('id', 'first_name', 'last_name', 'dob', 'city', 'state')->where('is_active', 1)->whereNull('deleted_at')->get();
        }
        $response = array();
        foreach ($data as $employee) {
            $response[] = array("value" => $employee->id, "label" => $employee->first_name . ' ' . $employee->last_name . ' (' . $employee->dob . ',' . $employee->city . ',' . $employee->state . ')');
        }
        return response()->json($response);
    }

    public function fetchData(Request $request)
    {

        $response = array();
        $address = '';
        $hospiceName = '';
        $carekit = '';
        $data = Patients::where('id', $request->id)->get();
        foreach ($data as $key => $value) {
            $newleaf_facility_id = $value->newleaf_facility_id;
        }
        if ($newleaf_facility_id) {
            $carekit = CareKit::select('hospice_care_kit_id', 'name')->where('facility_id', $newleaf_facility_id)->where('is_active', 1)->get();
            $carekitArray = array();
            foreach ($carekit as $key => $value) {
                $carekitArray[$value["hospice_care_kit_id"]] = $value["name"];
            }
        }
        foreach ($data as $key => $value) {
            $response['fname'] = $value->first_name ?? "";
            $response['lname'] = $value->last_name ?? "";
            $response['dob'] = $value->dob ?? "";
            $response['id'] = $value->newleaf_customer_id ?? "";
            $response['phone_number'] = $value->phone_number ?? "";
            $response['pharmacy_id'] = $value->pharmacy_id ?? "";
            $address = (isset($value->address_1) && $value->address_1 != " ") ? trim($value->address_1) . ' ' : "";
            $address .= (isset($value->address_2) && $value->address_2 != " ") ? trim($value->address_2) . ' ' : "";
            $address .= (isset($value->city) && $value->city != " ") ? trim($value->city) . ' ' : "";
            $address .= (isset($value->state) && $value->state != " ") ? trim($value->state) . ' ' : "";
            $address .= (isset($value->country) && $value->country != " ") ? trim($value->country) . ' ' : "";
            $address .= (isset($value->zipcode) && $value->zipcode != " ") ? trim($value->zipcode) : "";
            $response['address'] = trim($address);
            if ($value->facility_code) {
                $hospiceId = $this->branchService->fetchInformation($value->facility_code)['hospice_id'] ?? "";
                if ($hospiceId) {
                    $hospiceName = $this->hospiceService->fetchInformation($hospiceId)['name'];
                    if ($hospiceName) {
                        $response['hospiceName'] = $hospiceName;
                    }
                }
            }
            $data = Rxs::where('customer_id', $response['id'])
                ->leftJoin('drugs', 'drugs.newleaf_drug_id', '=', 'rxs.prescribed_drug_id')
                ->leftJoin('prescriber', 'prescriber.prescriber_id', '=', 'rxs.prescriber_id');
            $data = $data->get();
            $dropDown = '<button type="button" class="btn btn-primary open_drug_modal" data-toggle="modal" >
            Select Drug ..</button>';
            $logo = config('app.APP_LOGO');
            $modal = '<div id="myModal"  class="modal fade" tabindex="-1">
            <div class="modal-dialog" style="width:70%; height:70%; margin:0px!important;">
                <div class="modal-content" style="display: inline-table; position:fixed; min-height: 0; ">
                    <div class="modal-header">
                    <img width="120" height="30" src="' . $logo . '">
                        <h5 class="modal-title">Order Items</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body" id="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
                    <table class="table tableVal tablesorter" id="myTable">
                        <thead>
                        <tr>
                          <th>Rx #</th>
                          <th>Drug</th>
                          <th>Direction</th>
                          <th>Fill Date</th>
                          <th>Qty Disp</th>
                          <th>Fills Owed</th>
                          <th>Pat Amt</th>
                          <th>Clinical</th>
                          <th>Prescriber</th>
                        </tr>
                      </thead>
                      <tbody>';
            foreach ($data as $employee) {
                $date1 = Carbon::createFromFormat('m/d/Y', date("m/d/Y"));
                $date2 = Carbon::createFromFormat('m/d/Y', date('m/d/Y', strtotime($employee->date_expires)));
                $dateResult = $date2->gt($date1);
                $is_cancelled_bool = $employee->Is_cancelled == "False";
                $refillRemaining = $employee->refills_remaining > 0;
                $status = ($employee->status == 9) ? false : true;
                $dateValText = '';
                if ($dateResult && $is_cancelled_bool && $refillRemaining && $status) {
                    $dateValText = '<td style="color:green;">Active</td>';
                    $dataStyle = 'style="pointer-events:fill;" class="rxs_val_clickable"';
                } else {
                    $dateValText = '<td style="color:red;">Expired</td>';
                    $dataStyle = 'style="pointer-events:none;" class="rxs_val_clickable bg-grey"';
                }
                $date_written = Carbon::parse($employee->date_written);
                // $dropDown .= '<a href="'.route('fetchData').'">';
                $modal .= '<tr ' . $dataStyle . '   id="rxs_val" data-id="' . $employee->rx_id . '"><td>' . $employee->rx_number . '</td>';
                $modal .= '<td>' . $employee->description . '</td>';
                $modal .= '<td>' . $employee->original_sig . '</td>';
                $modal .= '<td>' . $date_written->format('M d Y') . '</td>';
                $modal .= '<td>' . $employee->verified_quantity_dispensed . '</td>';
                $modal .= '<td>' . floor($employee->refills_remaining) . '</td>';
                $modal .= '<td>' . '$0.00' . '</td>';
                $modal .= $dateValText;
                $modal .= '<td>' . $employee->first_name . ' ' . $employee->last_name . '</td></tr>';
                // $dropDown .= '</a>';
            }
            $modal .= '</tbody></table>
                    </div>
                    <div class="modal-footer">
                    <div  class="btn btn-outline-info btn-block"  type="button">
                    Note : <br>
                        1) Clinical indication specify by parameters like medication date expiry , availability , etc.. <br>
                        2) grey row indicates non-selective drug.
                    </div>
                    </div>
                </div>
            </div>
        </div>';
            $response['rxs'] = $dropDown;
            $response['modal'] = $modal;
            if ($newleaf_facility_id) {$response['carekit'] = $carekitArray;}
        }

        return response()->json($response);
    }

    public function autocompletePrescriber(Request $request)
    {

        $input = $request->all(); 
        $prefix = $input['prefix'];

        $data = Prescriber::leftJoin('prescriber_address', 'prescriber_address.prescriber_id', 'prescriber.prescriber_id')
        ->where('prescriber_address.is_primary', 1);

        if ($prefix) {
            $result = array();
            preg_match('/^(.+?),(.+)$/', $prefix, $result);
            $lastShortname = $result[1] ?? "";
            $firstShortname = $result[2] ?? "";
            if ($lastShortname && $firstShortname) {
                $data = $data->where(DB::raw('CONCAT(last_name)'), 'LIKE', $lastShortname . '%')
                    ->Where(DB::raw('CONCAT(first_name)'), 'LIKE', $firstShortname . '%');
            } else {
                $data = $data->where(DB::raw('CONCAT(last_name)'), 'LIKE', $prefix . '%');
            }
            $data = $data->get();
        } else {
            $data = Prescriber::orderby('first_name', 'asc')->select('id', 'first_name', 'last_name')->get();
        }
        $response = array();
        foreach ($data as $employee) {
            $response[] = array("value" => $employee->prescriber_id, "label" => $employee->first_name . ' ' . $employee->last_name . ' (' . $employee->dea_number . ',' . $employee->state . ')');
        }
        return response()->json($response);
    }

    public function fetchDataPrescriber(Request $request)
    {
        $response = array();
        $address = '';
        $drug = '';
        $prescriberData = Prescriber::where('prescriber_id', $request->id)->first();
        $response['name'] = $prescriberData['first_name'] . ' ' . $prescriberData['last_name'] ?? "";
        $response['dea_number'] = $prescriberData['dea_number'] ?? "";
        $drug = DB::table('prescriber_address')->where('prescriber_id', '=', $prescriberData['prescriber_id'])->first();
        $address = isset($drug->address_1) ? $drug->address_1 : "";
        $address .= (isset($drug->address_2) && $drug->address_2 != " ") ? ',' . $drug->address_2 : "";
        $response['address'] = $address;
        $response['city'] = isset($drug->city) ? $drug->city : "";
        $response['state'] = isset($drug->state) ? $drug->state : "";
        $response['zipcode'] = isset($drug->zipcode) ? $drug->zipcode : "";

        return response()->json($response);
    }

    public function autocompleteByCustomerID(Request $request)
    {
        $input = $request->all();
        $prefix = $input['prefix'];
        $customerId = $input['id'];
        $data = Rxs::where('customer_id', $customerId)
            ->leftJoin('drugs', 'drugs.newleaf_drug_id', '=', 'rxs.prescribed_drug_id');
        $data = $data->where(function ($query) use ($prefix) {
            $query->orWhere('description', 'LIKE', "%{$prefix}%")->orWhere('rx_number', 'LIKE', "%{$prefix}%");
        });
        $data = $data->get();
        foreach ($data as $employee) {
            $response[] = array("value" => $employee->rx_id, "label" => $employee->description);
        }
        return response()->json($response);
    }

    public function fetchDrugData(Request $request)
    {
        $input = $request->all();
        $prefix = $input['prefix'];
        $data = Drugs::select('description');
        $data = $data->where(function ($query) use ($prefix) {
            $query->orWhere('description', 'LIKE', "{$prefix}%");
        });
        $data = $data->get();
        foreach ($data as $employee) {
            $response[] = array("value" => $employee->description, "label" => $employee->description);
        }
        return response()->json($response);
    }

    public function fetchDrugDetails(Request $request)
    {
        $response = array();
        $drug = Rxs::where('rx_id', $request['id'])->first();
        if (isset($drug->prescribed_drug_id)) {
            $drugDetails = Drugs::where('newleaf_drug_id', $drug->prescribed_drug_id)->first();
        } else {
            return [];
        }
        if ($drug) {
            $response['sig'] = $drug->original_sig ?? "";
            $response['fill'] = floor($drug->original_quantity) ?? "";
            $response['supply'] = $drug->original_days_supply ?? "";
            $response['owed'] = $drug->verified_quantity_dispensed ?? "";
            $response['refill'] = floor($drug->refills_remaining) ?? "";
            $response['rx_id'] = $request['id'];
            $response['rx_number'] = $drug->rx_number ?? "";
            $response['drug_id'] = $drug->prescribed_drug_id ?? "";
            $response['medication_name'] = $drugDetails->description ?? "";
        }
        return response()->json($response);
    }

    public function submitPlaceOrderForm(Request $request)
    {
        $input = $request->all();
        $result = $this->offlineOrderService->addInformation($input);
        self::tiffDownload($result, false);
        // Save activity for new hospice added
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$result];
        $this->activityService->logs('added', config('app.activityModules')["Telephonic-Orders"], config('app.activityModules')["Telephonic-Orders"], config('app.activityModules')["Telephonic-Orders"], $keyForAddOperation, $valueForAddOperation);

        $notification = array(
            'message' => config('message.offlineOrderMgt.created'),
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

   
    public function generatePDF($id, $fortiff = false)
    {
        
        $modal = OfflineOrderItems::where('offline_order_id', $id)->get(); //to check for rx_type
        $countCK = 0;
        $isCareKit = '';

        $model = OfflineOrder::where('id', $id)->first(); //dd($model);
        $modelShippingAddress = $model->shipping_address;
        $modelPatientId = $model->patient_id;

        // We need the pharmacy the patient belongs to. // Pharmacy 1(NJ), 7(CA), 8(AZ)
        $patientModel = Patients::where('newleaf_customer_id', $modelPatientId)->first(); //dd($patientModel);
        $patientPharmacyId = $patientModel->pharmacy_id; //dd($pharmacyId);
        $patientState = $patientModel->state;


        // Current date and time
        $datetime = date("Y-m-d h:i:s");

        // We need to fetch the time the order was placed.
        $offlineOrderTime =  $model->time; 

        // Convert offlineOrderTime to Unix timestamp
        $timestamp = strtotime($offlineOrderTime); //dd($timestamp);
                    
        // Subtract time from datetime: Based on Pharmacy #
        if($patientPharmacyId == '7' || $patientPharmacyId == 7 || $patientPharmacyId == '8' || $patientPharmacyId == 8){
            $time = $timestamp - (3 * 60 * 60);
        } else if($patientPharmacyId == '1' || $patientPharmacyId == 1){
            $time = $timestamp; //dd($time);
        } else {
            $time = $timestamp - (1 * 60 * 60);
        }

        // time after subtraction
        $datetime = date("h:i:s", $time); //dd($datetime);
            
        $isUrgent = 0;
        if (!empty($model)) {
            $isUrgent = $model->is_urgent ?? 0;
        }
        if ($isUrgent) {
            $fileNamingConvension = $model->firstname . '_' . $model->lastname . '_' . strtoupper($model->shipping_method) . '_' . $id . '_URGENT';
        } else {
            $fileNamingConvension = $model->firstname . '_' . $model->lastname . '_' . strtoupper($model->shipping_method) . '_' . $id;
        }

        foreach ($modal as $key => $val) {
            if ($val->rx_type == '1') {
                //$isCareKit = 'CK';
                $countCK++; 
            }
        }
        $modelItems = [];
        $table_2_data = [];

        if($countCK !=0){
            $modelItems = OfflineOrderItems::where('offline_order_id', $id)->orderBy('rx_type', 'ASC')->take($countCK)->get(); // Updated to display the total # of CK items rows & both must match - michmar
            $modelItemsChunks = OfflineOrderItems::where('offline_order_id', $id)->orderBy('rx_type', 'ASC')->get()->chunk($countCK); // Updated to display the total # of CK items rows & both must match - michmar
        } else {
            $modelItems = OfflineOrderItems::where('offline_order_id', $id)->orderBy('rx_type', 'ASC')->take(8)->get(); // Updated to display 10 rows instead of 12 & both must match - michmar
            $modelItemsChunks = OfflineOrderItems::where('offline_order_id', $id)->orderBy('rx_type', 'ASC')->get()->chunk(8); // Updated to display 10 rows instead of 12 & both must match - michmar    
        }
        $chunks = [];
        $chunks = $modelItemsChunks->map(function ($chunk) {
            return $chunk = $chunk->values();
        });
        if ($model) {
            $shippingMethodArr = config('app.shipping_methods'); //dd($shippingMethodArr);
            $shippingMethodTopLabel = '';
            $shipping_method = $shippingMethodArr[$model->shipping_method] ?? '';
            $alertSymbol = '**************************';
            if (($model->shipping_method == 'ES2') || ($model->shipping_method == 'RST') || ($model->shipping_method == 'FD2')) {
                $shippingMethodTopLabel = $alertSymbol . '' . $shipping_method . '' . $alertSymbol;
            }
            // Added this when order is check marked as Urgent 4/28/23 - michmar
            $urgent = '';
            if ($model->is_urgent == 1) {
                $urgent = '***************************URGENT*****************************';
            }
            //////////////////////////////////////////////////////////////////////////
            if (!empty($model->dob)) {
                $arrDateInfo = explode(" ", $model->dob);
                $dob = $arrDateInfo[0];
            } else {
                $dob = "";
            }

            $data = [
                //'main_note' => '', // remove this per Tim 4/27/23
                'main_date' => getFormatedDate($model->date), 
                //'main_time' => $model->time,
                'main_time' => $datetime,
                'main_rph' => $model->rph,
                'last_name' => $model->lastname,
                'first_name' => $model->firstname,
                'patient_dob_id' => $dob . ' #' . $model->patient_id,
                'shipping_address' => $model->shipping_address,
                'name_of_hospice' => $model->hospice_name,
                'name_phone' => $model->rn_name_details,
                'prescriber_dea' => $model->prescriber_dea,
                'prescriber_name' => $model->prescriber_name,
                'address' => $model->prescriber_address,
                'city' => $model->prescriber_city,
                'state' => $model->prescriber_state,
                'zip' => $model->prescriber_zip,
                'ship_method' => $shipping_method,
                'ship_req' => $model->signature == 'Y' ? 'YES' : 'NO',
                'ship_note' => $model->notes,
                'shippingMethodTopLabel' => $shippingMethodTopLabel,
                'is_urgent' => $urgent,
                'is_careKit' => $isCareKit,
                'countCK' => $countCK,
                'table_2_data' => $modelItems,
                'chunks' => $chunks
            ];
        }
        $pdf = PDF::loadView('placeOrder', $data);
        if ($fortiff) {
            return $pdf;
        } else {
            return $pdf->stream($fileNamingConvension . '.pdf');
        }
    }

    
    public function tiffDownload($id, $download = true)
    {
        $pdf = self::generatePDF($id, true);
        // Set time since model time has come in null
        $datetime = date("h:i:s");
        // We need to track the count of Care Kit
        $countCK = 0; $isCareKit = '';
        // We need to check if it's urgent
        $isUrgent = 0;

        $modal = OfflineOrderItems::where('offline_order_id', $id)->get();

        foreach ($modal as $key => $val) {
            if ($val->rx_type == '1') {
                $isCareKit = 'CK';
                $countCK++; 
            }
        }

        //$model = OfflineOrder::where('id', $id)->first()->get();
        $model = OfflineOrder::where('id', $id)->get();

        foreach($model as $value){
            //dd($value);
            $fName = $value['firstname'] ?? "";
            $lName = $value['lastname'] ?? "";

            $shippingAddress = $value['shipping_address'] ?? "";
            $prescriberState = $value['prescriber_state'] ?? "";
            $shippingMethod = $value['shipping_method'] ?? "";
            $is_urgent = $value['is_urgent'] ?? "";
        }

        // AZ, HI, TX  = Are Grouped together
        // CA, ID, WA = Are Grouped together
        // All others go to NJ
        $stateAZ = "AZ"; $stateCA = "CA"; $stateHI = "HI"; $stateID = "ID"; $stateTX = "TX"; $stateWA = "WA"; 
        $stateaz = "az"; $stateca = "ca"; $statehi = "hi"; $stateid = "id"; $statetx = "tx"; $statewa = "wa"; 
        $stateAz = "Az"; $stateCa = "Ca"; $stateHi = "Hi"; $stateId = "Id"; $stateTx = "Tx"; $stateWa = "Wa";

        $state = $prescriberState ?? "";

        if (!empty($prescriberState)) {
            if(is_numeric($prescriberState)){
                $modelShippingAddress = $shippingAddress ?? "";
            } else {
                if($prescriberState == " "){
                    $modelShippingAddress = $shippingAddress ?? "";
                } else {
                    if($state = $stateAZ || $state == $stateCA || $state = $stateHI || $state == $stateID || $state = $stateTX || $state == $stateWA || 
                    $state = $stateaz || $state == $stateca || $state = $statehi || $state == $stateid || $state = $statetx || $state == $statewa) { 
                        $modelShippingAddress = $prescriberState ?? "";
                    } else {
                        $modelShippingAddress = $shippingAddress ?? "";
                    }                    
                }
            }
        } else {
            $modelShippingAddress = $shippingAddress ?? "";
        }

        try {
            if (!empty($model)) { $isUrgent = $is_urgent ?? 0;}

            if ($isUrgent) {
                if (isset($fName, $lName)) {
                    $fileNamingConvension = $fName . '_' . $lName . '_' . strtoupper($shippingMethod) . '_' . $id . '_URGENT';                
                } elseif (isset($lName)) {
                    $fileNamingConvension = $lName . '_' . strtoupper($shippingMethod) . '_' . $id . '_URGENT';
                } elseif (isset($fName)) {
                    $fileNamingConvension = $fName . '_' . strtoupper($shippingMethod) . '_' . $id . '_URGENT';                                
                } else {
                    //$fileNamingConvension = $model->time . '_' . strtoupper($model->shipping_method) . '_' . $id . '_URGENT'; 
                    $fileNamingConvension = $datetime . '_' . strtoupper($shippingMethod) . '_' . $id . '_URGENT';                
                }               
            } else {
                if (isset($fName, $lName)) {
                    $fileNamingConvension = $fName . '_' . $lName . '_' . strtoupper($shippingMethod) . '_' . $id;                
                } elseif (isset($lName)) {
                    $fileNamingConvension = $lName . '_' . strtoupper($shippingMethod) . '_' . $id;
                } elseif (isset($fName)) {
                    $fileNamingConvension = $fName . '_' . strtoupper($shippingMethod) . '_' . $id;                                
                } else {
                    //$fileNamingConvension = $model->time . '_' . strtoupper($model->shipping_method) . '_' . $id; 
                    $fileNamingConvension = $datetime . '_' . strtoupper($shippingMethod) . '_' . $id;                
                }               
            }
    
            // Makes Address/state into an array.
            $shippingAddy_array = explode(" ",$modelShippingAddress);
            // Does the state match CA or WA ? 
            $find = [$stateAZ, $stateCA, $stateHI, $stateID, $stateTX, $stateWA, $stateaz, $stateca, $statehi, $stateid, $statetx, $statewa, $stateAz, $stateCa, $stateHi, $stateId, $stateTx, $stateWa]; 
    
            // Loops through the array and matches to CA or WA
            $isFound = false;
            $goToAZ = false;
            foreach($find as $value) {
                if(in_array($value, $shippingAddy_array)) 
                {    
                    if($value == "AZ" || $value == "HI" || $value == "TX" || $value == "Az" || $value == "Hi" || $value == "Tx" || $value == "az" || $value == "hi" || $value == "tx") {
                        $goToAZ = true;
                    }
    
                    $isFound = true;
                    break;
                }
            }
    
            if($isFound) { 
                if($goToAZ){ 
                    $pdf->save(public_path('order_pdf/AZ/' . $fileNamingConvension . '.pdf'));
    
                    $im = new Imagick();
                    $im->setResolution(230, 230); 
                    $im->readImage(public_path('order_pdf/AZ/' . $fileNamingConvension . '.pdf'));
    
                    $im->setCompression(Imagick::COMPRESSION_LZW);
    
                    if($countCK !=0){
                        if($countCK > 10){ 
                            
                        } else {
                            $im->setImageExtent(1902, 1950); //w, H
                        }
                    }else{
                        $im->setImageExtent(1850, 1750); //w, H
                    }
        
                    $im->writeImages(public_path('order_tiff/AZ/' . $fileNamingConvension . '.tiff'), false);
    
                } else {
                    // If CA or WA are found, do this
                    $pdf->save(public_path('order_pdf/CA/' . $fileNamingConvension . '.pdf'));
                    
                    $im = new Imagick();
                    $im->setResolution(230, 230); 
                    $im->readImage(public_path('order_pdf/CA/' . $fileNamingConvension . '.pdf'));
    
                    $im->setCompression(Imagick::COMPRESSION_LZW);
    
                    if($countCK !=0){
                        if($countCK > 10){ 
                            
                        } else {
                            $im->setImageExtent(1902, 1950); //w, H
                        }
                    }else{
                        $im->setImageExtent(1850, 1750); //w, H
                    }
        
                    $im->writeImages(public_path('order_tiff/CA/' . $fileNamingConvension . '.tiff'), false);
                }
                
            } else { 
                // Else if NOT CA/WA save to NJ path.
                $pdf->save(public_path('order_pdf/NJ/' . $fileNamingConvension . '.pdf'));
    
                $im = new Imagick();
                $im->setResolution(230, 230); 
    
                $im->readImage(public_path('order_pdf/NJ/' . $fileNamingConvension . '.pdf'));
    
                $im->setCompression(Imagick::COMPRESSION_LZW);
    
                if($countCK !=0){
                    if($countCK > 10){ 

                    } else {
                        $im->setImageExtent(1902, 1950); //w, H
                    }
                }else{
                    $im->setImageExtent(1850, 1750); //w, H
                }
    
                $im->writeImages(public_path('order_tiff/NJ/' . $fileNamingConvension . '.tiff'), false); 
    
    
            }
    
            //This function discards the contents
            ob_end_clean(); 
    
            $headers = array('Content-Type: image/tiff', );
            
            if ($download) {
                if($isFound){
                    if($goToAZ){
                        return response()->download(public_path('order_tiff/AZ/' . $fileNamingConvension . '.tiff'), $fileNamingConvension . '.tiff', $headers);
                    } else {
                        return response()->download(public_path('order_tiff/CA/' . $fileNamingConvension . '.tiff'), $fileNamingConvension . '.tiff', $headers);
                    }
                } else{
                    return response()->download(public_path('order_tiff/NJ/' . $fileNamingConvension . '.tiff'), $fileNamingConvension . '.tiff', $headers);
                }            
            } else {
                return true;
            } 
    
        } catch (\Exception $e) {

            // Writing errors to the $log_file path
            $datetime = date("Y-m-d h:i:s");
            // error message to be logged
            $error_message = "offline_order_id#: $id. Error: $e";

            // path of the log file where errors need to be logged
            $log_file = '/home/beta-delivercare/public_html/storage/logs/laravel.log';
            //$log_file = '/home/beta-delivercare/logs/error_log';

            // setting error logging to be active
            ini_set("log_errors", TRUE); 
            
            // setting the logging file in php.ini
            ini_set('error_log', $log_file);
            
            //dd($error_message);
            // logging the error
            error_log($error_message);
            

            $data = ['MESSAGE' => $e ?? "", 'EMAIL' => 'mmartinez@delivercarerx.com'];
            EmailTemplatesRepository::sendMail('tiff-pdf-issue', $data);
        }

    }

    public function fetchOrderItems(Request $request)
    {
        $modal = OfflineOrderItems::where('offline_order_id', $request->id)->get();
        $data = '<table id="fetchOrderitems" style="font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;">
        <tr>
            <th style="border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;">MEDICATION NAME</th>
            <th style="border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;">RX Type</th>
            <th style="border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;">Direction</th>
            <th style="border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;">Written Qty</th>
            <th style="border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;">Fill Qty</th>
            <th style="border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;">Days Supply</th>

            <th style="border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;">Refills Qty</th>
        </tr>';
        foreach ($modal as $key => $val) {
            $rx_type_val = '';
            if ($val->rx_type == '1') {
                $rx_type_val = 'N';
            } elseif ($val->rx_type == '2') {
                $rx_type_val = 'C';
            } elseif ($val->rx_type == '3') {
                $rx_type_val = 'R';
            }
            $data .= '
            <tr>
              <td style="border: 1px solid #dddddd;
              text-align: left;
              padding: 8px;">' . $val->drug_name . '</td>
              <td style="border: 1px solid #dddddd;
              text-align: left;
              padding: 8px;">' . $rx_type_val . '</td>
              <td style="border: 1px solid #dddddd;
              text-align: left;
              padding: 8px;">' . $val->direction . '</td>
              <td style="border: 1px solid #dddddd;
              text-align: left;
              padding: 8px;">' . $val->written_qty . '</td>
              <td style="border: 1px solid #dddddd;
              text-align: left;
              padding: 8px;">' . $val->fill_qty . '</td>
              <td style="border: 1px solid #dddddd;
              text-align: left;
              padding: 8px;">' . $val->original_days_supply . '</td>
              <td style="border: 1px solid #dddddd;
              text-align: left;
              padding: 8px;">' . $val->refills . '</td>
            </tr>';
        }
        $data .= '</table>';
        return $data;
    }

    public function upsDetails(Request $request)
    {
        $model = '';
        $radioButtonText = '';
        $uds_monday_friday = '';
        $uds_saturday = '';
        $roverCount = '';

        $patient_id = isset($request->patient_id) ? $request->patient_id : "";
        if ($patient_id) {
            $model = Patients::select('cities.name as p_c_name', 'states.code as p_s_code', 'countries.shortname as c_shortname', 'pharmacy.zipcode', 'patients.state as p_state', 'patients.city as p_city', 'patients.zipcode as p_zipcode', 'patients.country as p_country', 'patients.shipping_method as shipping_method_p')->where('newleaf_customer_id', $patient_id)
                ->leftJoin('branch', 'branch.id', '=', 'patients.facility_code')
                ->leftJoin('pharmacy', 'pharmacy.pharmacy_newleaf_id', '=', 'branch.pharmacy_newleaf_id')
                ->leftJoin('cities', 'cities.id', 'pharmacy.city_id')
                ->leftJoin('states', 'states.id', 'pharmacy.state_id')
                ->leftJoin('countries', 'countries.id', 'pharmacy.country_id')
                ->first();

            if ($model->p_zipcode) {
                if ($model->shipping_method_p == 5) {
                    $endpointurl = config('app.ups_config.endPointUrl'); # $uri  # Config
                    $requestXML = "<?xml version='1.0'?><AccessRequest xml:lang='en-US'>
                                <AccessLicenseNumber>" . config('app.ups_config.AccessLicenseNumber') . "</AccessLicenseNumber>
                                <UserId>" . config('app.ups_config.UserId') . "</UserId>
                                <Password>" . config('app.ups_config.Password') . "</Password>
                                </AccessRequest>
                                <?xml version='1.0'?>
                                <TimeInTransitRequest xml:lang='en-US'>
                                <Request>
                                <TransactionReference>
                                <CustomerContext>TNT Service Adjustment</CustomerContext>
                                <XpciVersion>1.0002</XpciVersion>
                                </TransactionReference>
                                <RequestAction>TimeInTransit</RequestAction>
                                </Request>

                                <TransitFrom>
                                <AddressArtifactFormat>
                                <PoliticalDivision2>" . $model->p_c_name . "</PoliticalDivision2>
                                <PoliticalDivision1>" . $model->p_s_code . "</PoliticalDivision1>
                                <CountryCode>" . $model->c_shortname . "</CountryCode>
                                <PostcodePrimaryLow>" . $model->p_zipcode . "</PostcodePrimaryLow>
                                </AddressArtifactFormat>
                                </TransitFrom>

                                <TransitTo>
                                <AddressArtifactFormat>
                                <PoliticalDivision2>" . $model->p_city . "</PoliticalDivision2>
                                <PoliticalDivision1>" . $model->p_state . "</PoliticalDivision1>
                                <CountryCode>" . $model->p_country . "</CountryCode>
                                <PostcodePrimaryLow>" . $model->p_zipcode . "</PostcodePrimaryLow>
                                </AddressArtifactFormat>
                                </TransitTo>

                                <PickupDate>" . date('Ymd') . "</PickupDate>
                                </TimeInTransitRequest>";

                    // create Post request
                    $form = array(
                        'http' => array(
                            'method' => 'POST',
                            'header' => 'Content-type: application/x-www-form-urlencoded',
                            'content' => $requestXML
                        )
                    );

                    $request = stream_context_create($form);
                    $browser = fopen($endpointurl, 'rb', false, $request);
                    if (!$browser) {
                        throw new \Exception("Connection failed.");
                    }

                    // get response
                    $response = stream_get_contents($browser);
                    $resp = new \SimpleXMLElement($response);
                    $responseCode = $resp->Response->ResponseStatusCode;
                    if ($responseCode == 1) {
                        $radioButtonText = '<li class="d-inline-block mr-2 mb-1"><fieldset><div class="radio radio-shadow"><input type="radio" value="ups_ground" id="ups_ground" name="shipping_method" checked><label for="ups_ground">UPS Ground</label></div></fieldset></li>';
                    } else if ($responseCode != 1) {
                        $radioButtonText = '<li class="d-inline-block mr-2 mb-1"><fieldset><div class="radio radio-shadow"><input type="radio" value="ups_next_day" id="ups_next_day" name="shipping_method" checked><label for="ups_next_day">UPS Next Day</label></div></fieldset></li>';
                    }
                } else {
                    $roverCount = DB::table('rover')->where('zip', '=', $model->p_zipcode)->count();
                    $udsVal = DB::table('uds')->where('zip', '=', $model->p_zipcode)->first();
                    if ($udsVal) {
                        $uds_monday_friday = $udsVal->monday_friday == 'Y' ? 1 : 0;
                        $uds_saturday = $udsVal->saturday == 'Y' ? 1 : 0;
                        if ($uds_monday_friday) {
                            $radioButtonText .= '<li class="d-inline-block mr-2 mb-1"><fieldset><div class="radio radio-shadow"><input type="radio" value="uds_monday_friday" id="uds_monday_friday" name="shipping_method" checked><label for="uds_monday_friday">Monday To Friday (UDS)</label></div></fieldset></li>';
                        }
                        if ($uds_saturday) {
                            $radioButtonText .= '<li class="d-inline-block mr-2 mb-1"><fieldset><div class="radio radio-shadow"><input type="radio" value="uds_saturday" id="uds_saturday" name="shipping_method" checked><label for="uds_saturday">Saturday (UDS)</label></div></fieldset></li>';
                        }
                    }
                    if ($roverCount) {
                        $radioButtonText .= '</i><li class="d-inline-block mr-2 mb-1"><fieldset><div class="radio radio-shadow"><input type="radio" value="rover" id="rover" name="shipping_method" checked><label for="rover">Rover</label></div></fieldset></li>';
                    }
                    if (empty($uds_monday_friday) && empty($uds_saturday) && ($roverCount == 0)) {
                        $endpointurl = config('app.ups_config.endPointUrl'); # $uri  # Config
                        $requestXML = "<?xml version='1.0'?><AccessRequest xml:lang='en-US'>
                                    <AccessLicenseNumber>" . config('app.ups_config.AccessLicenseNumber') . "</AccessLicenseNumber>
                                    <UserId>" . config('app.ups_config.UserId') . "</UserId>
                                    <Password>" . config('app.ups_config.Password') . "</Password>
                                    </AccessRequest>
                                    <?xml version='1.0'?>
                                    <TimeInTransitRequest xml:lang='en-US'>
                                    <Request>
                                    <TransactionReference>
                                    <CustomerContext>TNT Service Adjustment</CustomerContext>
                                    <XpciVersion>1.0002</XpciVersion>
                                    </TransactionReference>
                                    <RequestAction>TimeInTransit</RequestAction>
                                    </Request>

                                    <TransitFrom>
                                    <AddressArtifactFormat>
                                    <PoliticalDivision2>" . $model->p_c_name . "</PoliticalDivision2>
                                    <PoliticalDivision1>" . $model->p_s_code . "</PoliticalDivision1>
                                    <CountryCode>" . $model->c_shortname . "</CountryCode>
                                    <PostcodePrimaryLow>" . $model->p_zipcode . "</PostcodePrimaryLow>
                                    </AddressArtifactFormat>
                                    </TransitFrom>

                                    <TransitTo>
                                    <AddressArtifactFormat>
                                    <PoliticalDivision2>" . $model->p_city . "</PoliticalDivision2>
                                    <PoliticalDivision1>" . $model->p_state . "</PoliticalDivision1>
                                    <CountryCode>" . $model->p_country . "</CountryCode>
                                    <PostcodePrimaryLow>" . $model->p_zipcode . "</PostcodePrimaryLow>
                                    </AddressArtifactFormat>
                                    </TransitTo>

                                    <PickupDate>" . date('Ymd') . "</PickupDate>
                                    </TimeInTransitRequest>";

                        // create Post request
                        $form = array(
                            'http' => array(
                                'method' => 'POST',
                                'header' => 'Content-type: application/x-www-form-urlencoded',
                                'content' => $requestXML
                            )
                        );

                        $request = stream_context_create($form);
                        $browser = fopen($endpointurl, 'rb', false, $request);
                        if (!$browser) {
                            throw new \Exception("Connection failed.");
                        }

                        // get response
                        $response = stream_get_contents($browser);
                        $resp = new \SimpleXMLElement($response);
                        $responseCode = $resp->Response->ResponseStatusCode;
                        if ($responseCode == 1) {
                            $radioButtonText = '<li class="d-inline-block mr-2 mb-1"><fieldset><div class="radio radio-shadow"><input type="radio" value="ups_ground" id="ups_ground" name="shipping_method" checked><label for="ups_ground">UPS Ground</label></div></fieldset></li>';
                        } else if ($responseCode != 1) {
                            $radioButtonText = '<li class="d-inline-block mr-2 mb-1"><fieldset><div class="radio radio-shadow"><input type="radio" value="ups_next_day" id="ups_next_day" name="shipping_method" checked><label for="ups_next_day">UPS Next Day</label></div></fieldset></li>';
                        }
                    }
                }
            }
            return response()->json($radioButtonText);
        }
    }

    public function roverQuote(Request $request)
    {
        //dd($request);

        $model = '';
        //$roverQText = '';

        // Current date and time
        $date = date("Y-m-d"); 
        $time = date("H:i:s");
        $cenvertedTime = date('H:i:s',strtotime('+1 hour',strtotime($time)));
        $dateTime = $date . 'T' .$cenvertedTime; //dd($dateTime);

        //Rover Test AZ Addy
        $fg = 'FG';
        $null = '';
        $roverContact = 'ROVER 3PL';
        
        $patient_id = isset($request->patient_id) ? $request->patient_id : "";
        if ($patient_id) {
            $model = Patients::select('patients.first_name', 'patients.last_name', 'patients.address_1', 'patients.pharmacy_id', 
            'patients.address_2', 'patients.city', 'patients.state', 'patients.zipcode', 'patients.shipping_method as shipping_method_p',
            'pharmacy_id', 'pharmacy.name',
            'pharmacy.address_1 as pharm_address1','pharmacy.address_2 as pharm_address2','pharmacy.state_id as pharm_state',
            'pharmacy.city_id as pharm_city','pharmacy.zipcode as pharm_zip')
                ->where('newleaf_customer_id', $patient_id)
                //->leftJoin('branch', 'branch.id', '=', 'patients.facility_code')
                ->leftJoin('pharmacy', 'pharmacy.pharmacy_newleaf_id', '=', 'patients.pharmacy_id')
                //->leftJoin('cities', 'cities.id', 'pharmacy.city_id')
                //->leftJoin('states', 'states.id', 'pharmacy.state_id')
                //->leftJoin('countries', 'countries.id', 'pharmacy.country_id')
                ->get();

                foreach ($model as $key => $value) {
                    //dd($value);
                    $patientName = $value['first_name'] . ' ' . $value['last_name'];
                    $street = $value['address_1'];
                    $street2 = $value['address_2'];
                    $city = $value['city'];
                    $state = $value['state'];
                    $zip = $value['zipcode'];

                    $pharm_id = $value['pharmacy_id'];
                    $pharm_name = $value['pharmacy_name'];
                    $pharm_address1 = $value['pharm_address1'];
                    $pharm_address2 = $value['pharm_address2'];
                    $pharm_state = $value['pharm_state'];
                    $pharm_city = $value['pharm_city'];
                    $pharm_zip = $value['pharm_zip'];

                }
                //dd($pharm_id);
   
                if ($pharm_id == 8) { 
                    //dd($request['roverServices']);
                    if($request['roverServices'] == "EMERGENCY STAT(2 Hour)"){
                        $roverQuoteSelected = "EMERGENCY STAT";
                    } else if($request['roverServices'] == "STAT(4 Hour)"){
                        $roverQuoteSelected = "STAT";
                    } else if($request['roverServices'] == "AFTER HOURS(2 Hour)"){
                        $roverQuoteSelected = "AFTER HOURS";
                    }
                    
                    //$roverQuoteSelected = $request['roverServices']; //dd($roverQuoteSelected);
                   
                    //POST to get access token via Login
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => 'https://www.national-3pl-solutions.com/Axis/Login',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      //CURLOPT_TIMEOUT_MS => 8000,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_POSTFIELDS =>'grant_type=password&username=DCRXaxis101&password=DCRXaxis101@',
                      CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                        ),
                    )); 
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);

                    if ($err) {
                        echo "Error: " .$err;
                        $buttonText = '<span class="roverText">Service Unavailable</span>';
                    } else {
                        $results = json_decode($response, true);
                        //echo "succesful!"; var_dump($results);                          
                    }

                    
                    $token = $results['access_token'];
                    //POST to send Patient Info via access token from previous POST
                    
                    if($token != ''){
                        $curlQuote = curl_init();
                        $data = array(
                            "Vehicle" => "Car",
                            "ClientRefNo" => "9999999",
                            "PickupAddress" => array(                                
                                "Name" => "Rover Quote",
                                "Contact"=> $roverContact,
                                "Street" => $pharm_address1,
                                "Street2"=> $pharm_address2,
                                "City"=> $pharm_city,
                                "State"=> $pharm_state,
                                "Zip"=> $pharm_zip
                            ),
                            "DeliveryAddress" => array(
                                "Name" => $patientName,
                                "Contact" => $patientName,
                                "Street" => $street,
                                "Street2" => $street2,
                                "City" => $city,
                                "State" => $state,
                                "Zip" => $zip,
                                "Phone" => "",
                                "Fax" => "",
                                "Email" => "",
                                "SpecialInstr" => ""
                            ),
                            "PickupTargetFrom" => $dateTime,
                            "PickupTargetTo" => $dateTime,
                            "DeliveryTargetFrom" => $dateTime,
                            "DeliveryTargetTo" => $dateTime,
                            "OrderPackageItems" => array(
                                "Leg_PD" => true,
                                "Leg_RT" => false,
                                "PackageName" => "Pharmacy Bag",
                                "RefNo" => "999999",
                                "Weight" => 0.0,
                                "Length" => 0.0,
                                "Width" => 0.0,
                                "Height" => 0.0
                            ),                        
                            "Service" => $roverQuoteSelected
                        );


                        /*foreach($data as $keys => $value){
                            echo "<pre>";
                            var_dump($value);
                            echo "</pre>";
                        }*/

                        $postData = json_encode($data); 
                        //dd($postData);

        

                        curl_setopt_array($curlQuote, array(
                            CURLOPT_URL => 'https://www.national-3pl-solutions.com/Axis/v2/Quote/QuoteShipment',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => $postData,                            

                            CURLOPT_HTTPHEADER => array(
                              "Authorization: Bearer $token",
                              'Content-Type: application/json',
                            ),
                          )); 
      
                          $responseQuote = curl_exec($curlQuote);
                          $errQuote = curl_error($curlQuote);
                          curl_close($curlQuote);
      
                          if ($errQuote) {
                              echo "Quote Error: " .$errQuote; //dd($errQuote);
                              $buttonText = '<span class="roverText">Service Unavailable: Error getting Rover Quote</span>';
                          } else {
                              $quoteResults = json_decode($responseQuote, true);
                              //echo " quote succesful!"; 
                              //var_dump($quoteResults);                          
                          }

                          if(!empty($quoteResults['GrandTotal'])){
                            $grandTotal = $quoteResults['GrandTotal']; 
                          }
                          if(isset($quoteResults['MileageTotal'])){
                            $mileageTotal = $quoteResults['MileageTotal']; 
                          }

                          if(!empty($quoteResults['GrandTotal'])){
                            $buttonText = '<span class="roverSelectedText" style="overflow: hidden; color:transparent;">' .$roverQuoteSelected. "</span> Grand Total: $$grandTotal. Mileage Total: $mileageTotal";  
                            //dd($buttonText);
                          }  else {
                            $buttonText = '<span class="roverText">Service Unavailable</span>';                  
                          }                           
    
                    } else {
                        $buttonText = '<span class="roverText">Service Unavailable</span>';  
                    }
                } else {
                    $buttonText = '<span class="roverText">Service Unavailable</span>';  //dd($buttonText);

                }
        }

        
        return response()->json($buttonText);
    }

    public function fetchCareItems(Request $request)
    {
        $id = $request['id'];
        $model = CareKitItems::where('hospice_care_kit_id', $id)->get();
        foreach ($model as $key => $value) {
            if ($value->drug_id) {
                $drug = Drugs::where('newleaf_drug_id', $value->drug_id)->first();
                if ($drug) {
                    $drugName = $drug['description'] ?? "";
                }
            }
            if ($value) {
                $response[$key]['medication_name'] = $drugName ?? "";
                $response[$key]['sig'] = $value->sig ?? "";
                $response[$key]['fill'] = floor($value->quantity) ?? "";
            }
        }
        return $response;
    }
}
