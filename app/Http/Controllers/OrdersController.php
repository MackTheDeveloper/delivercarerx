<?php

namespace App\Http\Controllers;


use App\Models\Branch;
use App\Models\Facility;
use App\Models\Hospice;
use App\Models\User;
use App\Models\NurseBranch;
use App\Models\Pharmacy;
use App\Models\Shipping;
use App\Exports\OrdersExport;
use App\Exports\OrdersNumbersExport;
use App\Models\Patients;
use App\Models\RefillOrder;
use App\Models\RefillOrderItems;
use App\Service\FacilityService;
use App\Service\HospiceService;
use App\Service\RoleService;
use App\Service\UserService;
use App\Service\BranchService;
use App\Service\ActivityService;
use App\Service\OfflineOrderService;
use App\Service\PatientService;
use App\Service\RefillOrderService;

use Illuminate\Support\Facades\Validator;
use App\Imports\NewLeafOrdersImport;

use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel as Excel2;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use Session;
use Response;
use Excel;
use Illuminate\Support\Carbon;
use PDF;
use Imagick;
use File;
use DB;


class OrdersController extends Controller

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
    public function __construct(UserService $userService, RoleService $roleService, FacilityService $facilityService, HospiceService $hospiceService, BranchService $branchService, ActivityService $activityService, RefillOrderService $refillOrderService, OfflineOrderService $offlineOrderService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->facilityService = $facilityService;
        $this->hospiceService = $hospiceService;
        $this->branchService = $branchService;
        $this->activityService = $activityService;
        $this->offlineOrderService = $offlineOrderService;
        $this->refillOrderService = $refillOrderService;
    }
    
    
    /**
     * Listing of the nurse
     *
     * @param Request $request
     */
    public function indexLatestSA(Request $request)
    {
        $hospiceId = '';
        $hospiceId = Auth::user()->hospice_id;
        if ($hospiceId) {
            $branch = $this->branchService->getDropDownListBranchAndHospice($hospiceId);
        } else {
            $branch = $this->branchService->getDropDownListBranchAndHospice();
        }
        $logistics = Shipping::all();
        return view('admin.orders.latest-orders', compact('branch', 'logistics'));
    }

    /**
     * Listing of the nurse
     *
     * @param Request $request
     * @return Response
     */
    public function listLatestSA(Request $request)
    {
        $result = $this->refillOrderService->fetchListing($request);
        return Response::json($result);
    }

    public function deleteLatestOrders(Request $request)
    {
        $result = $this->refillOrderService->deleteLatestOrders($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.hospital.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }

    /**
     * Listing of the nurse
     *
     * @param Request $request
     */
    public function indexAllSA(Request $request)
    {
        $hospiceId = '';
        $hospiceId = Auth::user()->hospice_id;
        if ($hospiceId) {
            $branch = $this->branchService->getDropDownListBranchAndHospice($hospiceId);
        } else {
            $branch = $this->branchService->getDropDownListBranchAndHospice();
        }
        $logistics = Shipping::all();
        return view('admin.orders.all-orders', compact('branch', 'logistics'));
    }

    /**
     * Listing of the nurse
     *
     * @param Request $request
     * @return Response
     */
    public function listAllSA(Request $request)
    {
        $result = $this->refillOrderService->fetchListingAll($request);
        return Response::json($result);
    }


    public function delete(Request $request)
    {
        $result = $this->refillOrderService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.hospital.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }

    /**
     * Listing of the nurse
     *
     * @param Request $request
     */
    public function indexOfflineOrders(Request $request)
    {
        $shippingMethodArr = config('app.shipping_methods');
        return view('admin.orders.offline-orders', compact('shippingMethodArr'));
    }

    /**
     * Listing of the nurse
     *
     * @param Request $request
     * @return Response
     */
    public function listOfflineOrders(Request $request)
    {
        $result = $this->offlineOrderService->fetchListing($request);
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


    public function updateStatus(Request $request)
    {
        //dd($request);
        // Save activities for updated information
        $model = $this->hospiceService->fetchInformation($request->input('id'));
        $model->fill($request->input());
        $this->activityServie->logs('updated', config('app.activityModules')["Hospice"], $model, '', '', '');
        return $result = $this->refillOrderService->updateOrderStatus($request);
    }

    public function generateOrdersPDF($id,$fortiff = false)
    {
        $model = RefillOrder::where('id', $id)->first(); //dd($model);
        $orderNumber = $model->order_number;

        $fileNamingConvension = str_replace(' ', '_',$model->patient_name).'_'.strtoupper($model->shipping_method).'_'.$id;
        $modelItems = [];
        $table_2_data = [];

        // We need the identifier # from the refill_orders table and display it on the pdf/tiff
        $getIdentifier = RefillOrder::select('patient_id','patient_name','newleaf_order_number')
        ->where('order_number', $orderNumber)
        ->get()->toArray();

        foreach($getIdentifier as $key => $identifier){
            $newLeafNum = $identifier['newleaf_order_number'];
        }
        // END the PDF/TIFF code for Identifier #

        // We need to grab the notes if there is an API error to fill meds. Ex: Med was already filled.
        $modelRefill = RefillOrderItems::select('refill_order_items.notes')
        ->join('refill_orders','refill_orders.id','refill_order_items.refill_order_id')        
        ->where('refill_order_id', $id)
        ->get()->toArray();

        $notesInfo = '';
        $count = 0;

        foreach($modelRefill as $key => $refillItems){
            $rxNote = $refillItems['notes'];
            //$notesInfo .= $refillItems['notes'];
            $notesInfo .= implode('', $refillItems);

            // Track the # of Rx in order
            $count++;
        }
        // END code to fetch notes

        $modelItems = RefillOrderItems::where('refill_order_id', $id)->take(12)->get();
        $modelItemsChunks = RefillOrderItems::where('refill_order_id', $id)->get()->chunk(12);
        $chunks = [];
        $chunks = $modelItemsChunks->map(function ($chunk) {
            return $chunk = $chunk->values();
        });
        if ($model) {
            $shippingMethodTopLabel = '';
            $alertSymbol = '************************************************';
            if (($model->shipping_method == 'SameDay (Az Only)') || ($model->shipping_method == 'STAT 2 (Az Only)') || ($model->shipping_method == 'STAT 4 (Az Only)')) {
                $shippingMethodTopLabel = $alertSymbol . '' . $model->shipping_method . '' . $alertSymbol;
            }
            $splitName = explode(' ', $model->patient_name) ?? "";
            $pateintData = Patients::where('id', $model->patient_id)->first() ?? [];
            
            if(!empty($model->newleaf_customer_id))
            {
                //$pateint = $model->dob.' ' ?? '';
                //$pateint .= '#'.$model->newleaf_customer_id ?? '';
                $pateint = '#'.$model->newleaf_customer_id ?? '';

            }
            else
            {
                //$pateint = $pateintData->dob.' ' ?? '';
                //$pateint .= '#'.$pateintData->newleaf_customer_id ?? ''; 
                $pateint = '#'.$pateintData->newleaf_customer_id ?? '';  
 
            }
            
            $hospiceData = Hospice::where('id', $model->hospice_id)->first() ?? [];
            $hospiceName = $hospiceData->name ?? "";
            $order_data = explode(' ', $model->created_at);
            $order_date = explode("-", $order_data[0]);
            $date = $order_date[1] . "/" . $order_date[2] . "/" . $order_date[0];
            $time = $order_data[1];
            $address = isset($model->address_1) ? $model->address_1 . ',' : '';
            $address .= isset($model->address_2) ? $model->address_2 : '';
            $address .= isset($model->city) ? $model->city . ',' : '';
            $address .= isset($model->state) ? $model->state : '';
            $nurseName = isset($model->nurse_name) ? $model->nurse_name : '';
            $data = [
                'main_date' => getFormatedDate($date),
                'main_time' => $time,
                'last_name' => $splitName[1],
                'first_name' => $splitName[0],
                'patient_dob_id' => $pateint,
                'shipping_address' => $address,
                'name_of_hospice' => $hospiceName,
                'ship_method' => $model->shipping_method ?? "",
                'ship_req' => $model->signature == '1' ? 'YES' : 'NO',
                'ship_note' => $model->notes ?? '',
                'shippingMethodTopLabel' => $shippingMethodTopLabel,
                'nurseName' => $nurseName,
                'table_2_data' => $modelItems,
                'orderNumber' =>$orderNumber,
                'notesInfo' => $notesInfo,
                'rxCount' => $count,
                'newLeafNum' => $newLeafNum,
                'chunks' => $chunks
            ];
        }
        $pdf = PDF::loadView('refillOrder', $data);
        if ($fortiff) {
            return $pdf;
        } else {
            return $pdf->stream($fileNamingConvension.'.pdf');
        }
    }

       //public function generateOrdersTIFF($id, $download = true, $createTiffDueToError, $errorMessage, $reasonErrorMessage1)
       public function generateOrdersTIFF($id, $download = true)
    {
        /* NOTES:
            -If all items successful, add a 3 to the if_tiff_generated column for SUCCESSFUL
            -If 1 item is successful, and all others are not, add a 2 to the if_tiff_generated column for Partial SUCCESSFUL
            -1 in the if_tiff_generated column means the cron job picked it up.
        */  
        // $id comes from the refill_order table

        // Fetch the PDF() and start the create process.
        $pdf = self::generateOrdersPDF($id, true);

        $model = RefillOrder::where('id', $id)->first();
        // We need the order # 
        $orderNumber = $model['order_number'];

        // We need to grab the notes if there is an API error to fill meds. Ex: Med was already filled.
        $errMessage = false;
        $modelRefill = RefillOrderItems::select('refill_order_items.notes')
        ->join('refill_orders','refill_orders.id','refill_order_items.refill_order_id')        
        ->where('refill_order_id', $id)
        ->get()->toArray();

        $notesInfo = '';
        $count = 0;
        $skipTIFF = false;
        foreach($modelRefill as $key => $refillItems){
            $rxNote = $refillItems['notes'];            
            
            if (str_contains($rxNote, 'is not found.') || str_contains($rxNote, 'Unsuccessful') || str_contains($rxNote, 'was filled earlier today')) {
                $errMessage = true;
                // Track the # of errors
                $count++;
            }
        }

        /* We need to find the if_tiff_gen value
        $queryToCheckIfTiffValue = RefillOrder::select('patient_name','newleaf_order_number','patient_id','if_tiff_generated','order_number')
        ->where('refill_orders.order_number',$orderNumber)
        ->get()->toArray();

        foreach($queryToCheckIfTiffValue as $key => $value){
            $tiffValue = $value['if_tiff_generated'];
        }*/

        // If no errors and all is successful we don't create a TIFF
        if($rxNote != ''){
            if($count == 0){
                $skipTIFF = true;    
                // If all items successful, add a 3 to the if_tiff_generated column for SUCCESSFUL            
                $queryToFetchName = RefillOrder::select('patient_name','newleaf_order_number','patient_id','if_tiff_generated','order_number')
                ->where('refill_orders.order_number',$orderNumber)
                ->update(['refill_orders.if_tiff_generated' => '3']);
                echo "<pre>";
                echo "rxNote is NOT empty. Has a value 3: ";
                echo "</pre>";

            } else if($count > 0){
                // If only 1 item is successful, and all others aren't, add a 2 to the if_tiff_generated column for Partial            
                $queryToFetchName = RefillOrder::select('patient_name','newleaf_order_number','patient_id','if_tiff_generated','order_number')
                ->where('refill_orders.order_number',$orderNumber)
                ->update(['refill_orders.if_tiff_generated' => '2']);
                echo "<pre>";
                echo "This is partially successful, we add a value 2: ";
                echo "</pre>";

            }    
        }

        // We need to check for states and decide where to send PDF/TIFF
        $state = $model['state'] ?? "";
        $stateCA = "CA"; $stateID = "ID"; $stateWA = "WA"; 
        $stateca = "ca"; $stateid = "id"; $statewa = "wa"; 
        $stateCa = "Ca"; $stateId = "Id"; $stateWa = "Wa";

        if (!empty($model['state'])) {
            $modelShippingAddress = $model['state'] ?? "";  
        } else {
            $modelShippingAddress = "NJ";
        }

        $fileNamingConvension = str_replace(' ', '_',$model->patient_name).'_'.strtoupper($model->shipping_method).'_'.$id;

        // Makes Address/state into an array.
        $shippingAddy_array = explode(" ",$modelShippingAddress);
        // Does the state match CA or WA ? 
        $find = [$stateCA, $stateID, $stateWA, $stateca, $stateid, $statewa, $stateCa, $stateId, $stateWa]; 

        // Loops through the array and matches to CA or WA
        $isFound = false;
        foreach($find as $value) {
            if(in_array($value, $shippingAddy_array)) 
            {
                $isFound = true;
                break;
            }
        }

        if($isFound) { 
            $pdf->save(public_path('refill_orders_pdf/CA/'.$fileNamingConvension.'.pdf'));

            if(!$skipTIFF){
                //var_dump("here1");

                $im = new Imagick();
                //$im->setResolution(125,125);
                $im->setResolution(150,150);

                $im->readImage(public_path('refill_orders_pdf/CA/'.$fileNamingConvension.'.pdf'));

                $im->setCompression(Imagick::COMPRESSION_LZW);
                if(!$errMessage){
                    //var_dump("here2");

                    $im->writeImages(public_path('refill_orders_tiff/CA/'.$fileNamingConvension.'.tiff'),false);
                }
            }

            if($errMessage){
                $im->writeImages(public_path('refill_orders_tiff/CA/AttenTIFF/'.$fileNamingConvension.'.tiff'),false);
            }
            //dd($isFound);

        } else {
            $pdf->save(public_path('refill_orders_pdf/NJ/'.$fileNamingConvension.'.pdf'));

            if(!$skipTIFF){
                //var_dump("here1");

                $im = new Imagick();
                //$im->setResolution(125,125);
                $im->setResolution(150,150);

                $im->readImage(public_path('refill_orders_pdf/NJ/'.$fileNamingConvension.'.pdf'));

                $im->setCompression(Imagick::COMPRESSION_LZW);
                if(!$errMessage){
                    //var_dump("here2");

                    $im->writeImages(public_path('refill_orders_tiff/NJ/'.$fileNamingConvension.'.tiff'),false);
                }
            }

            if($errMessage){
                $im->writeImages(public_path('refill_orders_tiff/NJ/AttenTIFF/'.$fileNamingConvension.'.tiff'),false);
            }

        }

        if(!$skipTIFF){

            if (ob_get_contents()) ob_end_clean();

            $headers = array('Content-Type: image/tiff',);
        }

        if ($download)
        {
            if(!$skipTIFF){
                if($isFound){ 
                    if($errMessage){
                        //var_dump("here3");
                        return response()->download(public_path('refill_orders_tiff/CA/AttenTIFF/'.$fileNamingConvension.'.tiff'), $fileNamingConvension.'.tiff', $headers);
                    } else {
                        //var_dump("here4");

                        return response()->download(public_path('refill_orders_tiff/CA/'.$fileNamingConvension.'.tiff'), $fileNamingConvension.'.tiff', $headers);
                    }       
                } else{
                    if($errMessage){
                        //var_dump("here3");
                        return response()->download(public_path('refill_orders_tiff/NJ/AttenTIFF/'.$fileNamingConvension.'.tiff'), $fileNamingConvension.'.tiff', $headers);
                    } else {
                        //var_dump("here4");

                        return response()->download(public_path('refill_orders_tiff/NJ/'.$fileNamingConvension.'.tiff'), $fileNamingConvension.'.tiff', $headers);
                    }       
                }  
            }          
        }
        else
        {
        return true;
        }
    }
     
    public function import()
    {
        return view('admin.import.newleaforders');
    }

    public function importData(Request $request)
    {
        $validator = Validator::make(['file' => request()->file('file'), 'extension' => strtolower($request->file->getClientOriginalExtension())], ['file' => 'required', 'extension' => 'required|in:csv,xlsx,xls,ods']);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        $import = new NewLeafOrdersImport;
        Excel2::import($import, request()->file('file'));
        $collection = $import->getCommon();
        $scounter = 0;
        $fcounter = 0;
        $counter = 0;
        $countMissMatch = 0;
        $columnMismatchText = '';
        $countEmailExist = 0;
        $emailIdsText = '';
        $errors = [];

        foreach ($collection as $row) {

            $counter++;
            $flag = 'true';

            if ($row[0] == "" || $row[1] == "" || $row[2] == "") {
                $flag = 'false';
                $fcounter++;
                $errors['misMatch'][] = "Record is incomplete for Row - " . $counter . ". Please try again.";
            }

            // DeliverCare and NewLeaf Order Id
            $DeliverCareOrderNum = $row[0];
            $NewLeafOrderNum = $row[1];

            if(strlen($DeliverCareOrderNum) < 5)
            {
                $DeliverCareOrderNum = str_pad($DeliverCareOrderNum,5,"0",STR_PAD_LEFT);
            }

            DB::table('refill_orders')
                ->where('order_number', $DeliverCareOrderNum)
                ->update(['newleaf_order_number' => $NewLeafOrderNum]);

            $scounter++;
        }
        //audit trails for facilities
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$scounter, $fcounter];
        //$this->activityService->logs('import', config('app.activityModules')["Import-Newleaf-Orders"], '', config('app.activityModules')["Import-Newleaf-Orders"], $keyForAddOperation, $valueForAddOperation);

        $result = [
            'success' => $scounter,
            'failed' => $fcounter,
            'columnMismatch' => $countMissMatch,
            'columnMismatchText' => $columnMismatchText,
            'emailExist' => $countEmailExist,
            'emailIdsText' => $emailIdsText,
        ];
        return Response::json($result);
    }
    public function exportOrderNumbers(Request $request)
    {
        $fileName = '';
        $time = str_replace(' ', '', Carbon::now()->format('d m Y H:i:s'));
        $fileName = 'Order_Number_'.$time.'.csv';
        try {
            $val = Excel2::download(new OrdersNumbersExport('nonGlobal'),$fileName, \Maatwebsite\Excel\Excel::CSV);
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
    public function globalExportOrderNumbers(Request $request)
    {
        $fileName = '';
        $time = str_replace(' ', '', Carbon::now()->format('d m Y H:i:s'));
        $fileName = 'newleafordersids_'.$time.'.csv';
        try {
            $val = Excel2::store(new OrdersNumbersExport('global'),$fileName,'excel_uploads');
            return $val;
        } catch (\Exception $ex) {
            dd($ex);
        }
    }

}
