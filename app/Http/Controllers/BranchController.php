<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facilities;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Hospice;
use App\Models\Branch;
use Auth;
use Session;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

use App\Service\BranchService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\CountryService;
use App\Service\StateService;
use App\Service\HospiceService;
use App\Service\FacilityService;
use App\Service\ActivityService;

use App\Imports\BranchsImport;
use App\Models\Facility;
use App\Models\Pharmacy;
use Excel;
use Response;


class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $branchService, $countryService, $stateService, $cityService,$hospiceService,$facilityService,$activityServie;


    public function __construct(BranchService $branchService,HospiceService $hospiceService,FacilityService $facilityService , CountryService $countryService, StateService $stateService, CityService $cityService,ActivityService $activityServie)
    {
        $this->branchService = $branchService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
        $this->hospiceService = $hospiceService;
        $this->facilityService = $facilityService;
        $this->activityServie = $activityServie;
        
    }

    public function index(Request $request)
    {
        return view('admin.branch.list');
    }

    

    public function list(Request $request)
    {
       return $result = $this->branchService->fetchListing($request);
        return Response::json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            if (Auth::check()) {
                $countries = $this->countryService->getCountryList();
                $hospice = $this->hospiceService->getHospiceList();
                if(Auth::user()->user_type==2)
                {
                    $facilities = $this->hospiceService->getHospiceFacilitiesList(Auth::user()->hospice_id);
                } else{
                    $facilities = [];
                }
                
                return view('admin.branch.add',compact('countries','hospice','facilities'));
            }
                
            return view('admin.login');
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
                
                // $this->validate($request, [
                    
                //     'name'=>'bail|required',
                //     'code' => 'bail|required',
                //     'hospice_id' => 'bail|required',
                //     'facility_id' => 'bail|required',
                //     'address_1' => 'bail|required',
                //     'address_2' => 'bail|required',
                //     'country_id' => 'bail|required',
                //     'state_id' => 'bail|required',
                //     'city_id' => 'bail|required',
                //     'zipcode' => 'bail|required',
                //     'phone' => 'bail|required',
                //     'status' => 'bail|required'
                // ]);

                $validator = Validator::make($request->all(),Branch::Rules());
                if ($validator->fails()) {
                return \Redirect::back()->withInput()->withErrors($validator->errors());
                }
                $result = $this->branchService->addInformation($request);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.branch.created'),
                'alert-type' => 'success'
            );
           // Save activity for new hospice added
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$request->name, $request->code];
        $this->activityServie->logs('added', config('app.activityModules')["Branch"], '', config('app.activityModules')["Branch"], $keyForAddOperation, $valueForAddOperation);

            return redirect()->route('branch-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
        
                
    }

    public function importBranch(Request $request)
    {
        return view('admin.import.import-hospic-branches');
    }

    


// public function uploadBranch(Request $request)
//     {
//         try {
//             if ($request->hasFile('file')) {
//                 $import = new BranchImport();
//                 $extension = $request->file('file')->extension();
//                 $valid_extension = array("xls", "xlsx", "csv", "ods");

//                     if (!in_array($extension, $valid_extension))
//                     {
//                         $return['numerOfSuccess'] = 0;
//                     $return['numerOfFailed'] = 0;
//                     $return['somethingWentWrong'] = 'while trying to import. Please download the sample file and try again.';
//                     return $return;
//                     }

//                 if ($extension == "xlsx") {
//                     Excel::import($import, $request->file('file'), null, \Maatwebsite\Excel\Excel::XLSX);
//                 } elseif ($extension == "xls") {
//                     Excel::import($import, $request->file('file'));
//                 }
//                 $collection = $import->getCommon();
//                 $numerOfSuccess = 0;
//                 $numerOfFailed = 0;
//                 $i = 0;
//                 foreach ($collection as $row) {
                
//                     $branchData['name'] = trim($row[0]);
//                     $branchData['code'] = trim($row[1]);
//                     $branchData['address_1'] = trim($row[2]);
//                     $branchData['address_2'] = trim($row[3]);
                    
//                     // Fetch State ID from state name
//                     $stateData = $this->stateService->fetchStateDataFromName($row['4']);
//                     if (!$stateData) {
//                         $newStateData = $this->stateService->addNewState($row['4']);
//                         $branchData['state_id'] = $newStateData->id;
//                     } else {
//                         $branchData['state_id'] = $stateData->id;
//                     }

//                     // Fetch City ID from city name
//                     $cityData = $this->cityService->fetchCityDataFromName($branchData['state_id'], $row['5']);
//                     if (!$cityData) {
//                         $cityData['state_id'] = $branchData['state_id'];
//                         $cityData['name'] = $row['5'];
//                         $newCityData = $this->cityService->addNewCity($cityData);
//                         $branchData['city_id'] = $newCityData->id;
//                     } else {
//                         $branchData['city_id'] = $cityData->id;
//                     }

//                     $branchData['zipcode'] = trim($row[6]);
//                     $branchData['phone'] = trim($row[7]);
//                     $branchData['carrier'] = trim($row[8]);
//                     $branchData['country_id'] = 231;
//                     $branchData['created_by'] = Auth::user()->id;
//                     if($i==0)
//                     {
//                    // dd($row);
//                      if($row[0] != "Name" || $row[1] != "FacilityCode" || $row[2] != "Address_1" || $row[3] != "Address_2" || $row[4] != "State" || $row[5] != "City" || $row[6] != "Zipcode" || $row[7] != "Phone" || $row[8] != "Carrier")
//                      {
//                      $return['numerOfSuccess'] = 0;
//                     $return['numerOfFailed'] = 0;
//                     $return['somethingWentWrong'] = 'while trying to import. Please download the sample file and try again.';
//                     return $return;
                     
//                     } 
//                     } else{
//                         $response =  Branch::create($branchData);
//                         //dd($response);
//                         if ($response)
//                         $numerOfSuccess++;
//                         else
//                         $numerOfFailed++;
//                     }
                        
                
                    
                    
//                     $i++;
//                 }
//                 $return['numerOfSuccess'] = $numerOfSuccess;
//                 $return['numerOfFailed'] = $numerOfFailed;
//                 $return['somethingWentWrong'] = 0;
//                 return $return;
//             } 
//         } catch (\Maatwebsite\Excel\Validators\ValidationException $ex) {
//             $return['numerOfSuccess'] = 0;
//             $return['numerOfFailed'] = 0;
//             $return['somethingWentWrong'] = 1;
//             return $return;
//         }
//     }

    public function uploadBranch(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $import = new BranchImport();
                $extension = $request->file('file')->extension();
                if ($extension == "xlsx") {
                    Excel::import($import, $request->file('file'), null, \Maatwebsite\Excel\Excel::XLSX);
                } elseif ($extension == "xls") {
                    Excel::import($import, $request->file('file'));
                }
                $collection = $import->getCommon();
                dd($collection);
                $numerOfSuccess = 0;
                $numerOfFailed = 0;
                $somethingWentWrong = 0;
                $columnMismatch = 0;
                foreach ($collection as $row) {
                    if ($row == 'Column Mismatch') {
                        $columnMismatch = 1;
                        break;
                    }
                    $existRow = Branch::Where('name',$row[0])->where('code',$row[1])->count();
                    
                    $branchData['name'] = trim($row[0]);
                    $branchData['code'] = trim($row[1]);
                    $branchData['address_1'] = trim($row[2]);
                    $branchData['address_2'] = trim($row[3]);
                    
                    // Fetch State ID from state name
                    $stateData = $this->stateService->fetchStateDataFromName($row['4']);
                    if (!$stateData) {
                        $newStateData = $this->stateService->addNewState($row['4']);
                        $branchData['state_id'] = $newStateData->id;
                    } else {
                        $branchData['state_id'] = $stateData->id;
                    }

                    // Fetch City ID from city name
                    $cityData = $this->cityService->fetchCityDataFromName($branchData['state_id'], $row['5']);
                    if (!$cityData) {
                        $cityData['state_id'] = $branchData['state_id'];
                        $cityData['name'] = $row['5'];
                        $newCityData = $this->cityService->addNewCity($cityData);
                        $branchData['city_id'] = $newCityData->id;
                    } else {
                        $branchData['city_id'] = $cityData->id;
                    }

                    $branchData['zipcode'] = trim($row[6]);
                    $branchData['phone'] = trim($row[7]);
                    $branchData['carrier'] = trim($row[8]);
                    $branchData['country_id'] = 231;
                    $branchData['created_by'] = Auth::user()->id;
                    if($existRow<=0)
                    {
                        $response = $this->branchService->uploadInformation($branchData); 
                    } else{
                        $response = 'fail';
                    }
                    
                    if ($response == 'success')
                        $numerOfSuccess++;
                    else
                        $numerOfFailed++;
                }
                $return['numerOfSuccess'] = $numerOfSuccess;
                $return['numerOfFailed'] = $numerOfFailed;
                $return['somethingWentWrong'] = $somethingWentWrong;
                $return['columnMismatch'] = $columnMismatch;
                return $return;
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $ex) {
            $return['numerOfSuccess'] = 0;
            $return['numerOfFailed'] = 0;
            $return['somethingWentWrong'] = 1;
            $return['columnMismatch'] = 0;
            return $return;
        }
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$numerOfSuccess, $numerOfFailed];
        $this->activityService->logs('import', config('app.activityModules')["Import-Branches"], '', config('app.activityModules')["Import-Branches"], $keyForAddOperation, $valueForAddOperation);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $id = decrypt($id);
            $data = $this->branchService->fetchInformation($id);
            if ($data) {
                $countries = $this->countryService->getCountryList();
                $states = $this->stateService->getStateList($data->country_id);
                $cities = $this->cityService->getCityList($data->state_id);
                $hospice = $this->hospiceService->getHospiceList();
                $facilities = $this->hospiceService->getHospiceFacilitiesList($data->hospice_id);
                return view('admin.branch.edit', compact('countries', 'states', 'cities','hospice', 'facilities','data'));
            } else {
                abort('404');
            }
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request,$id)
    {
        $id = decrypt($id);
       
        $validator = Validator::make($request->all(),Branch::Rules());
                if ($validator->fails()) {
                return \Redirect::back()->withInput()->withErrors($validator->errors());
                }
         // Save activities for updated information
        $model = $this->branchService->fetchInformation($id);
        $model->fill($request->input());
        $this->activityServie->logs('updated', config('app.activityModules')["Branch"], $model, '', '', '');       
        $result = $this->branchService->updateInformation($request, $id);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.branch.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('branch-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Save activity for deleted
        $branchData = $this->branchService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$branchData->name];
        $this->activityServie->logs('deleted', config('app.activityModules')["Branch"], '', '', $keyForAddOperation, $valueForAddOperation);

        $result = $this->branchService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.branch.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
        
    }
     /**
     * import the nurses
     *
     * @param Request $request
     */
    public function import()
    {
        return view('admin.branch.import-branch');
    }

    /**
     * import the nurses store function
     *
     * @param Request $request
     * @return Response
     */
    public function importBranches(Request $request)
    {
        $validator = Validator::make(['file' => request()->file('file'), 'extension' => strtolower($request->file->getClientOriginalExtension())], ['file' => 'required', 'extension' => 'required|in:csv,xlsx,xls,ods']);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        $import = new BranchsImport;
        Excel::import($import, request()->file('file'));
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
            if (($row[0] == "" || $row[1] == "" || $row[2] == "" || $row[3] == "" || $row[4] == "" || $row[5] == "")) {
                $flag = 'false';
                $errors['misMatch'][] = "Record is incomplete for Row - " . $counter . ". Please try again.";
            }
            if ($flag == 'true') {
                $branch = new \App\Models\Branch;
                $branch->newleaf_id = $row[0];
                $branch->status = $row[1];
                $branch->name = $row[2];
                $branch->code = $row[5];
                if($row[3]) { 
                    $hospiceData = Facilities::where('hospice_group',$row[3])->first(); 
                    $f_id = $hospiceData['id'];
                    $h_id = $hospiceData['hospice_id'];
                }
                $facility_id = 0;
                if ($hospiceData){
                    $branch->hospice_id =  $hospiceData->hospice_id ?? "0";
                    if($f_id){
                        $branch->facility_id = $f_id;
                    }
                }

                $pharmacy_id = 0;
                if ($row[4])
                {
                    $pharmacyData = Pharmacy::where('name',$row[4])->first();
                    if($pharmacyData)
                    {
                        $branch->pharmacy_newleaf_id = $pharmacyData->pharmacy_newleaf_id;
                        $pharmacy_id = $pharmacyData->id;
                    }
                   
                }
                $branch->save();

                // update facility with pharmacy_id
                Facilities::where('hospice_group',$row[3])->update(['pharmacy_id' => $pharmacy_id]);

                $scounter++;
            } else {
                $fcounter++;
            }
        }
        // $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        // $valueForAddOperation = [$scounter, $fcounter];
        // $this->activityService->logs('import', config('app.activityModules')["Branch"], '', config('app.activityModules')["Branch"], $keyForAddOperation, $valueForAddOperation);
        if (!empty($errors['misMatch'])) {
            $countMissMatch = count($errors['misMatch']);
            $columnMismatchText = $errors['misMatch'];
        }
        $result = [
            'success' => $scounter,
            'failed' => $fcounter,
            'columnMismatch' => $countMissMatch,
            'columnMismatchText' => $columnMismatchText,
        ];
        return Response::json($result);

    }
}
