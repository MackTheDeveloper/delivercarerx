<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facilities;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Hospice;
use App\Models\hospital;
use Auth;
use Session;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

use App\Service\hospitalService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\CountryService;
use App\Service\StateService;
use App\Service\HospitalServices;
use App\Service\HospiceService;
use App\Service\FacilityService;


class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $hospitalService, $countryService, $stateService, $cityService,$hospiceService,$facilityService;


    public function __construct(hospitalService $hospitalService,HospiceService $hospiceService,FacilityService $facilityService , CountryService $countryService, StateService $stateService, CityService $cityService)
    {
        $this->hospitalService = $hospitalService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
        $this->hospiceService = $hospiceService;
        $this->facilityService = $facilityService;

    }

    public function index(Request $request)
    {
        return view('admin.hospital.list');
    }

    public function list(Request $request)
    {
       return $result = $this->hospitalService->fetchListing($request);
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
                $facilities = Facilities::all();
                return view('admin.hospital.add',compact('countries','hospice','facilities'));
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

                $validator = Validator::make($request->all(),hospital::Rules());
                if ($validator->fails()) {
                return \Redirect::back()->withInput()->withErrors($validator->errors());
                }
                $result = $this->hospitalService->addInformation($request);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.hospital.created'),
                'alert-type' => 'success'
            );

            return redirect()->route('hospital-list')->with($notification);
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
            $data = $this->hospitalService->fetchInformation($id);
            if ($data) {
                $countries = $this->countryService->getCountryList();
                $states = $this->stateService->getStateList($data->country_id);
                $cities = $this->cityService->getCityList($data->state_id);
                $hospice = $this->hospiceService->getHospiceList();
                $facilities = Facilities::all();
                return view('admin.hospital.edit', compact('countries', 'states', 'cities','hospice','facilities','data'));
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
        $validator = Validator::make($request->all(),hospital::Rules());
                if ($validator->fails()) {
                return \Redirect::back()->withInput()->withErrors($validator->errors());
                }
        $result = $this->hospitalService->updateInformation($request, $id);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.hospital.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('hospital-list')->with($notification);
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
        $result = $this->hospitalService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.hospital.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }


}
