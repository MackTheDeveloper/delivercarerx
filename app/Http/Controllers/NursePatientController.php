<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patients;
use App\Models\Branch;
use App\Models\Pharmacy;
use App\Models\Facilities;
use App\Models\Hospice;
use App\Service\ActivityService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\BranchService;
use App\Service\PatientService;
use App\Service\SyncService;
use App\Models\Cart;
use App\Models\CartMaster;
use App\Models\Drugs;
use App\Models\Rxs;
use App\Models\RefillOrder;
use App\Models\RefillOrderItems;
use App\Models\Refill;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Repository\CartRepository;
use App\Repository\CartMasterRepository;
use App\Repository\RefillOrderItemsRepository;
use App\Repository\RefillOrderRepository;
use Auth;
use Session;
use Schema;
use Response;
use App\Http\Controllers\OrdersController;

use PDF;
use Imagick;



class NursePatientController extends Controller
{

    protected  $activityServie, $branchService, $refillsInQueueService, $refillOrderRepo,$orderController,$hospiceService;


    /**
     * constructor for initialize Admin service
     *
     * @param HospiceService $hospiceService reference to hospiceService
     * @param CountryService $countryService reference to countryService
     * @param StateService $stateService reference to stateService
     * @param CityService $cityService reference to cityService
     * @param ActivityService $activityServie reference to activityServie
     * @param OrdersController $orderController
     *
     *
     */
    public function __construct(ActivityService $activityServie,BranchService $branchService, PatientService $patientService, CartRepository $cartRepo, CartMasterRepository $cartMasterRepo, SyncService $syncService, RefillOrderItemsRepository $refillOrderItemsRepo, RefillOrderRepository $refillOrderRepo,OrdersController $orderController)
    {
        $this->branchService = $branchService;
        $this->activityServie = $activityServie;
        $this->patientService = $patientService;
        $this->cartRepo = $cartRepo;
        $this->cartMasterRepo = $cartMasterRepo;
        $this->syncService = $syncService;
        $this->refillOrderItemsRepo = $refillOrderItemsRepo;
        $this->refillOrderRepo = $refillOrderRepo;
        $this->orderController = $orderController;

    }

    /**
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Does optumToken exist?
        $optumToken = '';
        if (isset($_GET['idToken'])) {
            $optumToken = $_GET['idToken']; //dd($optumToken);
         }

        $branch = $this->branchService->getDropDownListBranchAndHospice();
        return view('admin.nurseListing',compact('branch'))->with($optumToken);
        //return view('admin.nurseListing',compact('branch','optumToken'));

    }

    public function list(Request $request)
    {

        $result = $this->patientService->fetchListingPatients($request);
        return Response::json($result);
    }

    public function patientIndex(Request $request, $id)
    {
        //dd($id);
        $cartCustId = '';
        $cart_master_id = $request->session()->get('cart_master_id'); //dd($cart_master_id);
        $cartMasterData = CartMaster::selectRaw('cart_master.*')->where('id',$cart_master_id)->first();  //dd($cartMasterData);

        if(isset($cartMasterData)){
            $cartCustId = $cartMasterData->newleaf_customer_id; //dd($cartCustId);

        }


        $cartNotEmptyNotification = '';
        if($id != $cartCustId){
            $cartNotEmptyNotification = 'Only one patient’s prescription can be added to the cart at a time. Please complete the checkout process for the previous patient by clicking on the cart at the top right-hand corner.';
        }


        $routeUrl = route('patientsListData');
        $patientData = Patients::selectRaw('first_name,last_name,id')->whereNull('deleted_at')->where('newleaf_customer_id',$id)->first(); //dd($patientData);
        if ($patientData) {
            $patientName = $patientData->first_name;
            if(!empty($patientData->last_name)){
                $patientName .= " " . $patientData->last_name;    
            }
            $patientId = $patientData->id;
    
            $cartItems = array();
            $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->get()->toArray(); //dd($cartData);

            if(!empty($cartData)){
                return view('admin.nursePatientsListing',compact('id','patientName','routeUrl','patientId','cartData','cartNotEmptyNotification'));
            } else {
                return view('admin.nursePatientsListing',compact('id','patientName','routeUrl','patientId'));
            }
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /*
    public function patientIndex($id)
    {
        $routeUrl = route('patientsListData');
        $patientData = Patients::selectRaw('first_name,last_name,id')->whereNull('deleted_at')->where('newleaf_customer_id',$id)->first();
        if ($patientData) {
            $patientName = $patientData->first_name;
        if(!empty($patientData->last_name))
        {
            $patientName .= " " . $patientData->last_name;
        }
        $patientId = $patientData->id;
        return view('admin.nursePatientsListing',compact('id','patientName','routeUrl','patientId'));

        }
        else
        {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }*/

    public function patientList(Request $request)
    {
        $result = $this->patientService->fetchPatientsData($request);
        return Response::json($result);
    }

    //patient prescriptions
    public function patientPrescriptions($id)
    {
        // Fetch patient prescription detail
        $result = $this->patientService->fetchRxData($id);
        // dd($result);
        return view('admin.prescription',['result'=> $result,'id'=>$id]);
    }

    public function fillHistoryData(Request $request)
    {
        $result = $this->patientService->fetchListingFillHistory($request);
        return Response::json($result);
    }

    // add prescription cart
    function addCartRx(Request $request)
    {
        $req = $request->all();

        // take variables
        $rxNumber = $req['rxnumbers'];
        $rIds = $req['rIds']; // rIds is used for single checkbox on the nursePatient refill path 07/27
        $customer_id = $req['id'];
        $patient_id = $req['patient_id'];

        if(!empty($req['oneRx'])) {
            $oneRx = $req['oneRx']; // oneRx is used for single checkbox on the nursePatient refill path from Page 1 and you click on Page 2 and so on. 07/27
        }
        if(!empty($req['rxnums'])) {
            $rxnums = $req['rxnums']; // oneRx is used for single checkbox on the nursePatient refill path from Page 1 and you click on Page 2 and so on. 07/27
        }

        //dd("rxNumber: " .$rxNumber . " rIds: " . $rIds . " rxnums: " . $rxnums );

        /*if(empty($rxNumber)) {
            if($rIds != ''){ 
                $rxNumber = $rIds;
            }
        }*/

        if(!empty($req['oneRx'])) {
            $rxNumber = $rxNumber . ',' . $oneRx . ',' . $rIds; // oneRx is used for single checkbox on the nursePatient refill path from Page 1 and you click on Page 2 and so on. 07/27
        } else {
            $rxNumber = $rxNumber . ',' . $rIds; // oneRx is used for single checkbox on the nursePatient refill path from Page 1 and you click on Page 2 and so on. 07/27
        }
        //dd("Here 1: " . $rxNumber);

        $rxNumber = implode(',',array_unique(explode(',', $rxNumber))); // Added this to remove dups from adding more on the "My Shopping Cart"
        //dd("Here 2:: " . $rxNumber);

        // insert into cart table
        $arrRxs = explode(",",$rxNumber); //dd($arrRxs);

        $arrRxs = array_filter($arrRxs, function($arrRxs) { return ($arrRxs !== 0 AND trim($arrRxs) != ''); });
        //dd($arrRxs);


        // Get Patient details based on Rx
        foreach ($arrRxs as $key => $rxNum) {

            // Fetch RxData
            //$drugData = Rxs::selectRaw('rxs.*')->where('rx_number',$rxNum)->first(); //dd($drugData);
            $matchThese = ['rx_number' => $rxNum, 'customer_id' => $customer_id]; //dd($matchThese);
            $drugData = Rxs::selectRaw('rxs.*')->where($matchThese)->first(); //dd($drugData);



            // Fetch Drug ID
            if(!empty($drugData->customer_id))
            {
                $customer_id = $drugData->customer_id;

                // Get patient newleaf id
                $patNewLeaf = Patients::selectRaw('patients.*')
                    ->where('patients.newleaf_customer_id',$customer_id)
                    ->first(); //dd($patNewLeaf);

                if(!empty($patNewLeaf))
                {
                    $patient_id = $patNewLeaf->id;
                }
            }

            break;
        }

        // Check if session set
        $cart_master_id = $request->session()->get('cart_master_id');

        // Check if current patient is same as previous one
        if(!empty($cart_master_id))
        {
            $cartMasterData = CartMaster::selectRaw('cart_master.*')->where('id',$cart_master_id)->first();
            //dd($cartMasterData);

            $cartPatient_id = $cartMasterData->patient_id;

            if($patient_id != $cartMasterData->patient_id)
            {
                // Fetch all cart items
                $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->get();

                // delete all previous patient rxs because this is new patient
                foreach ($cartData as $key => $value) {
                    $this->cartRepo->delete($value->id);
                }
            }

            $this->cartMasterRepo->delete($cart_master_id);

            $cart_master_id = 0;
        }

        $pharmacyData = Patients::select('pharmacy.id')
        ->join('branch', 'branch.id', 'patients.facility_code')
        ->join('facilities', 'facilities.id', 'branch.facility_id')
        ->join('pharmacy', 'pharmacy.id', 'facilities.pharmacy_id')
        ->where('patients.id',$patient_id)
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

        // Logged in user id
        $user_id = Auth::user()->id;

        // Get patient newleaf id
        $patNewLeaf = Patients::selectRaw('patients.*')
        ->where('patients.id',$patient_id)
        ->first();

        if(!empty($patNewLeaf))
        {
            $pat_newleaf_id = $customer_id;
            $dob = $patNewLeaf->dob;
        }
        else
        {
            $pat_newleaf_id = 0;
            $dob = "";
        }


        if(empty($cart_master_id))
        {
            // Save cart master information
            $cartMData['user_id'] = $user_id;
            $cartMData['patient_id'] = $patient_id;
            $cartMData['newleaf_customer_id'] = $pat_newleaf_id;
            $cartMData['dob'] = $dob;
            $cartMData['flag_complete'] = "N";
            $response = $this->cartMasterRepo->create($cartMData);

            $cart_master_id = $response->id;
            $request->session()->put('cart_master_id', $cart_master_id);

            // insert into cart table
            $arrRxs = explode(",",$rxNumber);
            $arrRxs = array_filter($arrRxs, function($arrRxs) { return ($arrRxs !== 0 AND trim($arrRxs) != ''); }); // Added this to remove 0 records.

            foreach ($arrRxs as $key => $rxNum) {

                # Filter
                $filterSingle = "RxNumber eq ".$rxNum;

                $select = "VerifiedQuantityDispensed,OriginalDaysSupply,OriginalSIG";

                // Set API Request parameters
                $api_request_parameters = array(
                    '$filter' => $filterSingle,
                    '$select' => $select
                );

                // Fetch patient's rxs data
                $response = $this->syncService->fetchNewLeafData($pharmacy_id, $endpoint, $api_request_parameters);


                // Fetch RxData
               //$drugData = Rxs::selectRaw('rxs.*')->where('rx_number',$rxNum)->first();
               $matchThese = ['rx_number' => $rxNum, 'customer_id' => $customer_id]; //dd($matchThese);
               $drugData = Rxs::selectRaw('rxs.*')->where($matchThese)->first(); //dd($drugData);



                // RxId
                $rxIdNewLeaf = empty($drugData->rx_id) ? "" : $drugData->rx_id;

                // Get refill dispense qty
                $refillQty = Refill::selectRaw('refills.dispensed_quantity')->where('rx_id',$rxIdNewLeaf)->where('refill_number',0)->first();

                if(!empty($response['value']))
                {
                    $response = $response['value'][0];
                    $qty = empty($refillQty->dispensed_quantity) ? 0 : $refillQty->dispensed_quantity;
                    $days = empty($response['OriginalDaysSupply']) ? 0 : $response['OriginalDaysSupply'];
                    $direction = empty($response['OriginalSIG']) ? 0 : $response['OriginalSIG'];
                }
                else
                {
                    $qty = empty($refillQty->dispensed_quantity) ? 0 : $refillQty->dispensed_quantity;
                    $days = empty($drugData->original_days_supply) ? 0 : $drugData->original_days_supply;
                    $direction = empty($drugData->original_sig) ? 0 : $drugData->original_sig;
                }

                // Fetch Drug ID
                if(!empty($drugData->prescribed_drug_id))
                {
                    $drugInfo = Drugs::selectRaw('drugs.*')->where('newleaf_drug_id',$drugData->prescribed_drug_id)->first();
			//print_r($drugInfo);
                }

                $drugName = "";
                $drug_id = "";
                if(!empty($drugData->prescribed_drug_id))
                {
                    $drug_id = $drugData->prescribed_drug_id;
                }
                if(!empty($drugInfo->description))
                {
                     $drugName = $drugInfo->description;
                }

                // Save cart information
                $cartData = [];
                $cartData['cart_master_id'] = $cart_master_id;
                $cartData['rxnumber'] = $rxNum;
                $cartData['qty'] = $qty;
                $cartData['drug_id'] = $drug_id;
                $cartData['drug_name'] = $drugName;
                $cartData['days'] = $days;
                $cartData['direction'] = $direction;
                $response = $this->cartRepo->create($cartData);
            }
        }
        else
        {
            // update cart master records
            $cartMData = [];
            $cartMData['user_id'] = $user_id;
            $cartMData['patient_id'] = $patient_id;
            $cartMData['newleaf_customer_id'] = $pat_newleaf_id;
            $cartMData['flag_complete'] = "N";
            $response = $this->cartMasterRepo->update($cartMData,$cart_master_id);

            // insert into cart table
            $arrRxs = explode(",",$rxNumber);

            foreach ($arrRxs as $key => $rxNum) {

                # Filter
                $filterSingle = "RxNumber eq ".$rxNum;

                $select = "VerifiedQuantityDispensed,OriginalDaysSupply,OriginalSIG";

                // Set API Request parameters
                $api_request_parameters = array(
                    '$filter' => $filterSingle,
                    '$select' => $select
                );

                // Fetch patient's rxs data
                $response = $this->syncService->fetchNewLeafData($pharmacy_id, $endpoint, $api_request_parameters);

                // Fetch RxData
                //$drugData = Rxs::selectRaw('rxs.*')->where('rx_number',$rxNum)->first();
                $matchThese = ['rx_number' => $rxNum, 'customer_id' => $customer_id]; //dd($matchThese);
                $drugData = Rxs::selectRaw('rxs.*')->where($matchThese)->first(); //dd($drugData);


                // RxId
                $rxIdNewLeaf = empty($drugData->rx_id) ? "" : $drugData->rx_id;

                // Get refill dispense qty
                $refillQty = Refill::selectRaw('refills.dispensed_quantity')->where('rx_id',$rxIdNewLeaf)->where('refill_number',0)->first();

                if(!empty($response['value']))
                {
                    $response = $response['value'][0];
                    $qty = empty($refillQty->dispensed_quantity) ? 0 : $refillQty->dispensed_quantity;
                    $days = empty($response['OriginalDaysSupply']) ? 0 : $response['OriginalDaysSupply'];
                    $direction = empty($response['OriginalSIG']) ? 0 : $response['OriginalSIG'];
                }
                else
                {
                    $qty = empty($refillQty->dispensed_quantity) ? 0 : $refillQty->dispensed_quantity;
                    $days = empty($drugData->original_days_supply) ? 0 : $drugData->original_days_supply;
                    $direction = empty($drugData->original_sig) ? 0 : $drugData->original_sig;
                }

                $drugName = "";
                $drug_id = "";

                if(!empty($drugData->prescribed_drug_id))
                {
                    $drug_id = $drugData->prescribed_drug_id;
                    $drugInfo = Drugs::selectRaw('drugs.*')->where('newleaf_drug_id',$drugData->prescribed_drug_id)->first();
                    $drugName = $drugInfo->description;
                }

                // Fetch all cart itemd
                $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->where('rxnumber',$rxNum)->first();

                // if cart id exists with same rxnumber for this patient
                if(!empty($cartData)){

                    // Save cart information
                    //$cartQty['qty'] = $cartData->qty + 1;
                    //$response = $this->cartRepo->update($cartQty, $cartData->id);

                    // no need to plus qty
                }
                else
                {
                    // Save cart information
                    $cartData = array();
                    $cartData['cart_master_id'] = $cart_master_id;
                    $cartData['rxnumber'] = $rxNum;
                    $cartData['qty'] = $qty;
                    $cartData['drug_id'] = $drug_id;
                    $cartData['drug_name'] = $drugName;
                    $cartData['days'] = $days;
                    $cartData['direction'] = $direction;
                    $response = $this->cartRepo->create($cartData);
                }
            }
        }

        // Check if session set
        $cart_master_id = $request->session()->get('cart_master_id');

        // redirect to customer shopping address page
        return redirect()->route('my-shopping-cart');
    }

    public function myShoppingCart(Request $request)
    {
        
        $request->getPathInfo();
        $path = $request->path(); // Send this var to the shopping cart

        // Check if session set
        $cart_master_id = $request->session()->get('cart_master_id');
        $cartMasterData = CartMaster::selectRaw('cart_master.*')->where('id',$cart_master_id)->first(); // 07/21/23 - Added this to track and grab the patient_id

        $patientId = $cartMasterData->patient_id; //dd($patientId);
        $newLeafCustId = $cartMasterData->newleaf_customer_id;

        $patientData = Patients::selectRaw('first_name,last_name,id')->whereNull('deleted_at')->where('newleaf_customer_id',$newLeafCustId)->first(); //dd($patientData);

        $patientName =  $patientData->first_name . ' ' . $patientData->last_name; //dd($patientName);

        // if session is not set
        if(empty($cart_master_id))
        {
            // redirect to customer shopping address page
            return redirect()->route('nursePatients-list');
        }
        else
        {
            // fetch all cart prescriptions
            $cartItems = array();
            $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->get()->toArray();

            // delete all previous patient rxs because this is new patient
            foreach ($cartData as $key => $value) {
                $cartItems[] = $value;
                
                $rIds = $value['rxnumber']; 
                $rxnum = $value['rxnumber']; 

            }

            if(empty($cartItems))
            {
                // redirect to customer shopping address page
                return redirect()->route('nursePatients-list');
            }
        }

        //return view('admin.my-shopping-cart',compact('cartItems'));
        return view('admin.my-shopping-cart',compact('cartItems', 'cartMasterData','patientId','newLeafCustId','value', 'rIds', 'path', 'patientName'));

    }

    public function deleteCart(Request $request)
    {
        $req = $request->all();
        $id = $req['id'];
        $result = $this->cartRepo->delete($id);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.cartMsg.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }

    public function deleteCartAll(Request $request)
    {
        // Check if session set
        $cart_master_id = $request->session()->get('cart_master_id');

        // if session is not set
        if(empty($cart_master_id))
        {
            // redirect to customer shopping address page
            return redirect()->route('nursePatients-list');
        }
        else
        {
            // fetch all cart prescriptions
            $cartItems = array();
            $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->get()->toArray();

            // delete all previous patient rxs because this is new patient
            foreach ($cartData as $key => $value) {
                $result = $this->cartRepo->delete($value['id']);
            }
        }

        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.cartMsg.deletedAll');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }

    public function shippingAddress(Request $request)
    {
        $req = $request->all();
        // Check if session set
        $cart_master_id = $request->session()->get('cart_master_id');
         // if session is not set
        if(empty($cart_master_id))
        {
            // redirect to customer shopping address page
            return redirect()->route('nursePatients-list');
        }
        else
        {
             // fetch all cart prescriptions
            $cartItems = array();
            $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->get()->toArray();

            if(empty($cartData))
            {
                // redirect to customer shopping address page
                return redirect()->route('nursePatients-list');
            }
        }
        $patientId = CartMaster::selectRaw('cart_master.*')->whereNull('deleted_at')->where('id',$cart_master_id)->first();

        $addressData = $this->cartMasterRepo->fetch($cart_master_id);

        
        $patientData = $this->patientService->fetchInformation($patientId->patient_id);
        if(empty($addressData->address_1))
        {
            //save information in cart master table
            $cartData = array();
            $cartData['address_1'] = $patientData->address_1; 

            $cartData['address_1'] = rtrim($cartData['address_1'], ", ");
            //dd($cartData['address_1']);

            $cartData['address_2'] = $patientData->address_2;
            $cartData['state_code'] = $patientData->state;
            $cartData['city_code'] = $patientData->city;
            $cartData['zipcode'] = $patientData->zipcode;
            $cartData['patient_name'] = $patientData->first_name . " " . $patientData->last_name;

            $response = $this->cartMasterRepo->update($cartData, $cart_master_id);

            $addressData = $this->cartMasterRepo->fetch($cart_master_id);

        }else
        {
            $addressData = $this->cartMasterRepo->fetch($cart_master_id);
        }

        // Get shipping methods
        $shippingMethods = config('app.shipping_methods');

        return view('admin.address',compact('addressData','patientData','shippingMethods'));
    }

    public function shippingAddressDetails(Request $request)
    {
        $req = $request->all();

        // Check if session set
        $cart_master_id = $request->session()->get('cart_master_id');

        //save information in cart master table
        $cartData = array();
        $cartData['notes'] = $req['notes'];
        $cartData['signature'] = isset($req['signature']) ? 1 : 0;
        $cartData['shipping_method'] = $req['shipping_method'];

        $result = $this->cartMasterRepo->update($cartData, $cart_master_id);

        // redirect to review page
        return redirect()->route('cart-review');
    }

    public function cartReview(Request $request)
    {
        
        $req = $request->all();

        // Check if session set
        $cart_master_id = $request->session()->get('cart_master_id'); 
        
        // if session is not set
        if(empty($cart_master_id))
        {
            // redirect to customer shopping address page
            return redirect()->route('nursePatients-list');
        } else
        {

            // *NEW Look up shipping method in the cart master table
            $cartMasterData = CartMaster::selectRaw('cart_master.*')->whereNull('deleted_at')->where('id',$cart_master_id)->get();

            foreach($cartMasterData as $key => $value){
                $shippingMethod = $value->shipping_method;
            }

                // NOW we do a lookup in the patients 

            // end new









             // fetch all cart prescriptions
            $cartItems = array();
            $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->get()->toArray();

            //dd($cartData);


            if(empty($cartData))
            {
                // redirect to customer shopping address page
                return redirect()->route('nursePatients-list');
            }
        }

        $addressData = $this->cartMasterRepo->fetch($cart_master_id);

        $patientId = CartMaster::selectRaw('cart_master.*')->whereNull('deleted_at')->where('id',$cart_master_id)->first();
        $patientData = $this->patientService->fetchInformation($patientId->patient_id); 
        $patientState = $patientData->state;

        //return view('admin.review',compact('cartData','addressData'));
	    return view('admin.review',compact('cartData','addressData','patientData'));
    }

    public function placeOrder(Request $request)
    {
        $req = $request->all();
        // Check if session set
        $cart_master_id = $request->session()->get('cart_master_id');
        // if session is not set
        if(empty($cart_master_id))
        {
            // redirect to customer shopping address page
            return redirect()->route('nursePatients-list');
        } else
        {
             // fetch all cart prescriptions
            $cartItems = array();
            $cartData = Cart::selectRaw('cart.*')->whereNull('deleted_at')->where('cart_master_id',$cart_master_id)->get()->toArray();
            if(empty($cartData))
            {
                // redirect to customer shopping address page
                return redirect()->route('nursePatients-list');
            }
        }
        // Get cart master data
        $cartMasterData = $this->cartMasterRepo->fetch($cart_master_id);
        // Shipping Methods
        $shippingMethods = config('app.shipping_methods');
        // Get last order number
        $last_order_number = RefillOrder::selectRaw('refill_orders.order_number')->orderBy('id', 'DESC')->first();
        if(empty($last_order_number))
        {
            $orderNumber = "00001";
        }
        else
        {
            $orderNumber = $last_order_number->order_number + 1;
            if(strlen($orderNumber) < 5)
            {
                $orderNumber = str_pad($orderNumber,5,"0",STR_PAD_LEFT);
            }
        }
        // Get patient data and set hospice information with pharmacy id
        $patientInfo = Patients::selectRaw('patients.*')->whereNull('deleted_at')->where('id',$cartMasterData['patient_id'])->first();
        //echo $patientInfo;
        $facility_branch_id = empty($patientInfo->facility_code) ? 0 : $patientInfo->facility_code;
        // Get hospice id and pharmacy id
        $brandInfo = Branch::selectRaw('branch.*')->whereNull('deleted_at')->where('id',$facility_branch_id)->first();
        $hospice_id = empty($brandInfo->hospice_id) ? 0 : $brandInfo->hospice_id;
        $pharmacy_newleaf_id = empty($brandInfo->pharmacy_newleaf_id) ? 0 : $brandInfo->pharmacy_newleaf_id;
        $pharmacyInfo = Pharmacy::selectRaw('pharmacy.id')->whereNull('deleted_at')->where('pharmacy_newleaf_id',$pharmacy_newleaf_id)->first();
        $pharmacy_id = empty($pharmacyInfo->id) ? 0 : $pharmacyInfo->id;
        // Preparing data for refill orders
        $arrRefillOrder = array();
        $arrRefillOrder['patient_id'] = $cartMasterData['patient_id'];
        $arrRefillOrder['newleaf_customer_id'] = $cartMasterData['newleaf_customer_id'];
        $arrRefillOrder['dob'] = $cartMasterData['dob'];
        $arrRefillOrder['patient_name'] = $cartMasterData['patient_name'];
        $arrRefillOrder['hospice_id'] = $hospice_id;
        $arrRefillOrder['hospice_branch_id'] = $facility_branch_id;
        $arrRefillOrder['pharmacy_id'] = $pharmacy_id;
        $arrRefillOrder['order_number'] = $orderNumber;
        $arrRefillOrder['status'] = 2;
        $arrRefillOrder['shipping_name'] = $cartMasterData['patient_name'];
        $arrRefillOrder['address_1'] = $cartMasterData['address_1'];
        $arrRefillOrder['address_2'] = $cartMasterData['address_2'];
        $arrRefillOrder['city'] = $cartMasterData['city_code'];
        $arrRefillOrder['state'] = $cartMasterData['state_code'];
        $arrRefillOrder['zipcode'] = $cartMasterData['zipcode'];
        $arrRefillOrder['shipping_method_code'] = $cartMasterData['shipping_method'];
        $arrRefillOrder['shipping_method'] = $shippingMethods[$cartMasterData['shipping_method']];
        $arrRefillOrder['notes'] = $cartMasterData['notes'];
        $arrRefillOrder['signature_required'] = $cartMasterData['signature'];
        $arrRefillOrder['refilled_placed_online'] = 'Y';
        if(!empty(Auth::user()->first_name) && Auth::user()->last_name)
        {
            $arrRefillOrder['nurse_name'] = Auth::user()->first_name . " " . Auth::user()->last_name;
        } elseif(!empty(Auth::user()->name)) {
            $arrRefillOrder['nurse_name'] = Auth::user()->name;
        } else {
            $arrRefillOrder['nurse_name'] = "N/A";
        }
        //print_r($arrRefillOrder);
        $patient_id = $arrRefillOrder['patient_id']; //print_r($patient_id);
        $newleaf_customer_id = $arrRefillOrder['newleaf_customer_id']; //dd($newleaf_customer_id);
        // Create refill order
        $refill_order_id = $this->refillOrderRepo->create($arrRefillOrder);
        
        // Prepare refill order items
        $refillOrderItems = array();

        foreach($cartData as $k => $items)
        {
            //dd($items);
            // Get Rx and Drug Information
            $original_rx_date = "";
            $refill_remaining = "";
            $last_refill_date = "";
            //$rxInfo = Rxs::selectRaw('rxs.*')->where('rx_number',$items['rxnumber'])->first();
            $matchThese = ['rx_number' => $items['rxnumber'], 'customer_id' => $newleaf_customer_id]; //dd($matchThese);
            $rxInfo = Rxs::selectRaw('rxs.*')->where($matchThese)->first(); //dd($rxInfo);
            // Fetch Drug ID
            $drug_id = 0;
            $drugName = "";
            if(!empty($rxInfo->prescribed_drug_id))
            {
                $drugInfo = Drugs::selectRaw('drugs.*')->where('newleaf_drug_id',$rxInfo->prescribed_drug_id)->first();
                //echo $drugInfo;
                //echo "<br/><br/>";
                if(!empty($drugInfo->id))
                {
                    $drug_id = $drugInfo->id;
                    $drugName = $drugInfo->description;
                }
                $original_rx_date = $rxInfo->date_written;
                $refill_remaining = $rxInfo->refills_remaining;
                
            }
            // Get last refill date
            $lastRefill = RefillOrderItems::select('refill_order_items.created_at')
                    ->join('refill_orders', 'refill_orders.id', 'refill_order_items.refill_order_id')
                    ->where('refill_orders.rx_number',$items['rxnumber'])
                    ->where('patient_id', $cartMasterData['patient_id'])
                    ->orderBy('created_at', 'desc')
                    ->limit(1)
                    ->first();
            if(!empty($lastRefill))
            {
                $last_refill_date = $lastRefill->created_at;
            }
            // Preparing data for refill orders
            $arrRefillOrderItem = array();
            $arrRefillOrderItem['refill_order_id'] = $refill_order_id;
            $arrRefillOrderItem['rx_id'] = empty($rxInfo->id) ? 0 : $rxInfo->id;
            $arrRefillOrderItem['rx_number'] = $items['rxnumber'];
            $arrRefillOrderItem['drug_id'] = $drug_id;
            $arrRefillOrderItem['drug_name'] = $drugName;
            $arrRefillOrderItem['current_refill_date'] = date("Y-m-d");
            $arrRefillOrderItem['last_refill_date'] = $last_refill_date;
            $arrRefillOrderItem['original_rx_date'] = $original_rx_date;
            $arrRefillOrderItem['direction'] = $items['direction'];
            $arrRefillOrderItem['refill_left'] = $refill_remaining;
            $arrRefillOrderItem['quantity'] = $items['qty'];
            $this->refillOrderItemsRepo->create($arrRefillOrderItem);
        }
        
        // Create TIff
        //$this->orderController->generateOrdersTIFF($refill_order_id,true);
        // Mark cartmaster as complete
        $cartMData = array();
        $cartMData['flag_complete'] = "Y";
        $this->cartMasterRepo->update($cartMData,$cart_master_id);
        // unset session
        //Session::forget('cart_master_id');
        $request->session()->forget('cart_master_id');

        // redirect to review page
        return redirect()->route('thank-you', $orderNumber);
    }


    function thankYou(Request $request, $order_number)
    {
        return view('admin.thank-you',compact('order_number'));
    }
}

