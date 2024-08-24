<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facilities;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Hospice;
use App\Models\Pharmacy;
use Auth;
use Session;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Imports\FacilityImport;

use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Response;

use App\Service\FacilityService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\CountryService;
use App\Service\HospiceService;
use App\Service\StateService;
use App\Service\ActivityService;


class FacilitiesController extends Controller
{

    protected $facilityService, $hospiceService, $countryService, $stateService, $cityService, $activityService;


    public function __construct(FacilityService $facilityService, HospiceService $hospiceService,  CountryService $countryService, StateService $stateService, CityService $cityService, ActivityService $activityService)
    {
        $this->facilityService = $facilityService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
        $this->hospiceService = $hospiceService;
        $this->activityService = $activityService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {

        return view('admin.facility.list');
    }

    public function import()
    {
        return view('admin.import.facility');
    }


    public function importData(Request $request)
    {
        $validator = Validator::make(['file' => request()->file('file'), 'extension' => strtolower($request->file->getClientOriginalExtension())], ['file' => 'required', 'extension' => 'required|in:csv,xlsx,xls,ods']);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        $import = new FacilityImport;
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
            $email_arr = \App\Models\Facilities::select('*')->pluck('email')->toArray();
            $flag = 'true';

            if ($row[0]) {
                $hospiceModel =  Hospice::where('code', $row[0])->first();
            }

            if (($row[0] == "" || $row[1] == "" || $row[2] == "" || $row[3] == "" || $row[4] == "" || $row[5] == "" || $row[6] == "" || $row[7] == "" || $row[8] == "")) {
                $flag = 'false';
                $fcounter++;
                $errors['misMatch'][] = "Record is incomplete for Row - " . $counter . ". Please try again.";
            }

            $facility = new \App\Models\Facilities;
            $facility->hospice_client = $row[0];
            $facility->hospice_group = $row[1];
            $facility->name = $row[2];
            $facility->address_1 = $row[3];
            $facility->address_2 = $row[4];
            $facility->city_id = $row[5];
            $facility->state_id = $row[6];
            $facility->zipcode = $row[7];
            $facility->email = $row[8];
            $facility->hospice_id = $hospiceModel->id ?? "";
            $facility->save();
            $scounter++;
        }
        //audit trails for facilities
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$scounter, $fcounter];
        $this->activityService->logs('import', config('app.activityModules')["Import-Facility"], '', config('app.activityModules')["Import-Facility"], $keyForAddOperation, $valueForAddOperation);
        
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


    public function list(Request $request)
    {
        return $result = $this->facilityService->fetchListing($request);
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
                $pharmacy = Pharmacy::all();
                return view('admin.facility.add', compact('countries', 'hospice', 'pharmacy'));
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

        //                     'name'=>'bail|required',
        //                     'newLeaf_id' => 'bail|required',
        //                     'hospice_id' => 'bail|required',
        //                     'pharmacy_id' => 'bail|required',
        //                     'email' => 'bail|required',
        //                     'address_1' => 'bail|required',
        //                     'address_2' => 'bail|required',
        //                     'country_id' => 'bail|required',
        //                     'state_id' => 'bail|required',
        //                     'city_id' => 'bail|required',
        //                     'zipcode' => 'bail|required',
        //                     'phone' => 'bail|required',
        //                     'status' => 'bail|required'
        //                 ]);

        // Save activity for new hospice added
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$request->name, $request->code];
        $this->activityService->logs('added', config('app.activityModules')["Facilities"], '', config('app.activityModules')["Facilities"], $keyForAddOperation, $valueForAddOperation);
        $result = $this->facilityService->addInformation($request);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.facilities.created'),
                'alert-type' => 'success'
            );

            return redirect()->route('facilities-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
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
            $data = $this->facilityService->fetchInformation($id);
            if ($data) {
                $countries = $this->countryService->getCountryList();
                $states = $this->stateService->getStateList($data->country_id);
                $cities = $this->cityService->getCityList($data->state_id);
                $hospice = $this->hospiceService->getHospiceList();
                $pharmacy = Pharmacy::all();
                return view('admin.facility.edit', compact('countries', 'states', 'cities', 'hospice', 'pharmacy', 'data'));
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


    public function update(Request $request, $id)
    {
        $id = decrypt($id); 
        // Save activities for updated information
        $model = $this->facilityService->fetchInformation($id);
        $model->fill($request->input());
        $this->activityService->logs('updated', config('app.activityModules')["Facilities"], $model, '', '', '');

        $result = $this->facilityService->updateInformation($request, $id);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.facilities.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('facilities-list')->with($notification);
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
        $facilityData = $this->facilityService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$facilityData->name];
        $this->activityService->logs('deleted', config('app.activityModules')["Facilities"], '', '', $keyForAddOperation, $valueForAddOperation);

        $result = $this->facilityService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.facilities.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }
}
