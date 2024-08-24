<?php

namespace App\Http\Controllers;

use App\Imports\PatientImport;
use App\Models\Patients;
use App\Models\User;
use App\Service\ActivityService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\BranchService;
use App\Service\CityService;
use App\Service\CountryService;
use App\Service\HospiceService;
use App\Service\PatientService;
use App\Service\StateService;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;
use Excel;

class PatientController extends Controller
{

    protected $patientService, $countryService, $stateService, $cityService, $activityServie, $branchServie;

    /**
     * constructor for initialize Admin service
     *
     * @param PatientService $patientService reference to patientService
     * @param CountryService $countryService reference to countryService
     * @param StateService $stateService reference to stateService
     * @param CityService $cityService reference to cityService
     * @param ActivityService $activityServie reference to activityServie
     * @param BranchService $branchServie reference to branchServie
     * 
     */
    public function __construct(PatientService $patientService, CountryService $countryService, StateService $stateService, CityService $cityService, ActivityService $activityServie, BranchService $branchServie)
    {
        $this->patientService = $patientService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
        $this->activityServie = $activityServie;
        $this->branchServie = $branchServie;
    }

    /**
     * Show the import form
     *
     * @param  Request $request
     * @return Response
     */

     public function shipArray()
     {
        $ship_array = config('app.patient_shipping_method');
        echo $ship_array[1];
        // foreach ($ship_array as $k => $v)
        // {
        //     echo $k . '--'. $v. '<br>';
        // }
     }

    public function import(Request $request)
    {
        return view('admin.import.patients');
    }

    /**
     * Import data
     *
     * @param  Request $request
     * @return Response
     */
    public function importData(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $import = new PatientImport();
                $extension = $request->file('file')->extension();
                if ($extension == "xlsx") {
                    Excel::import($import, $request->file('file'), null, \Maatwebsite\Excel\Excel::XLSX);
                } elseif ($extension == "xls") {
                    Excel::import($import, $request->file('file'));
                }

                
                $collection = $import->getCommon();

                $numerOfSuccess = 0;
                $numerOfFailed = 0;
                $somethingWentWrong = 0;
                $columnMismatch = 0;
                foreach ($collection as $row) {
                    if ($row == 'Column Mismatch') {
                        $columnMismatch = 1;
                        break;
                    }
                    $patientData['first_name'] = trim($row[0]);
                    $patientData['last_name'] = trim($row[1]);
                    $patientData['middle_name'] = trim($row[2]);
                    $patientData['address_1'] = trim($row[3]);
                    $patientData['address_2'] = trim($row[4]);

                    // Fetch State ID from state name
                    $stateData = $this->stateService->fetchStateDataFromName($row['6']);
                    if (!$stateData) {
                        $newStateData = $this->stateService->addNewState($row['6']);
                        $patientData['state_id'] = $newStateData->id;
                    } else {
                        $patientData['state_id'] = $stateData->id;
                    }

                    // Fetch City ID from city name
                    $cityData = $this->cityService->fetchCityDataFromName($patientData['state_id'], $row['5']);
                    if (!$cityData) {
                        $cityData['state_id'] = $patientData['state_id'];
                        $cityData['name'] = $row['5'];
                        $newCityData = $this->cityService->addNewCity($cityData);
                        $patientData['city_id'] = $newCityData->id;
                    } else {
                        $patientData['city_id'] = $cityData->id;
                    }

                    // Fetch Branch ID from branch code
                    $branchData = $this->branchServie->fetchBranchDataFromcode($row['9']);
                    if (!$branchData) {
                        $patientData['facility_code'] = null;
                    } else {
                        $patientData['facility_code'] = $branchData->id;
                    }

                    $patientData['zipcode'] = trim($row[7]);
                    $patientData['phone_number'] = trim($row[8]);
                    $patientData['gender'] = (trim($row[10])) == 'Male' ? 1 : 2;
                    $patientData['dob'] = date('Y-m-d', strtotime(trim($row[11])));
                    $patientData['patient_id'] = trim($row[12]);
                    $patientData['patient_status'] = trim($row[13]);
                    $patientData['ipu'] = trim($row[14]);
                    $patientData['country_id'] = 231;

                    $response = $this->patientService->addInformation($patientData);
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
        $this->activityService->logs('import', config('app.activityModules')["Import-Patients"], '', config('app.activityModules')["Import-Patients"], $keyForAddOperation, $valueForAddOperation);
    }

    /**
     * Listing of the hospices
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return view('admin.patients.patient-list');
    }

    public function list(Request $request)
    {
        $result = $this->patientService->fetchListing($request);
        if ($result) {
            return Response::json($result);
        }
        else
        {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->route('patients-list')->with($notification);
        }
        

        // } else {
        //     $notification = array(
        //         'message' => config('message.somethingWentWrong'),
        //         'alert-type' => 'error'
        //     );
        //     return redirect()->route('patients-list')->with($notification);
        // }        
    }

    /**
     * store the hospice information
     *
     * @param  Request $request
     * @return Response
     */
     public function add(Request $request)
    {
        $countries = $this->countryService->getCountryList();
        $status = config('app.patients-status');
        $branch = $this->branchServie->getDropDownListBranchAndHospice(Auth::user()->hospice_id);
        $ship_array = config('app.patient_shipping_method');
        return view('admin.patients.patient-add', compact('countries','status','branch','ship_array'));
    }

    public function store(Request $request)
    {    
        $result = $this->patientService->addInformation($request->all());

       // Save activity 
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$request->first_name, $request->phone_number];
        $this->activityServie->logs('added', config('app.activityModules')["Patients"], '', config('app.activityModules')["Patients"], $keyForAddOperation, $valueForAddOperation);

        if ($result == 'success') {
            $notification = array(
                'message' => config('message.patientsMgt.created'),
                'alert-type' => 'success'
            );
            return redirect()->route('patients-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * Edit form for hospice information
     *
     * @param  Request $request
     * @param  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $model = $this->patientService->fetchInformation($id);
            if ($model) {
                $status = config('app.patients-status');
                $countries = $this->countryService->getCountryList();
                $states = $this->stateService->getStateList($model->country_id);
                $cities = $this->cityService->getCityList($model->state_id);
                $branch = $this->branchServie->getDropDownListBranchAndHospice();
                $ship_array = config('app.patient_shipping_method');
                return view('admin.patients.patient-edit', compact('countries', 'states', 'cities', 'model','status','branch','ship_array'));
            } else {
                abort('404');
            }
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * update the hospice information
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        // Save activities for updated information
        $model = $this->patientService->fetchInformation($request->id);
        $model->fill($request->input());
        $this->activityServie->logs('updated', config('app.activityModules')["Patients"], $model, '', '', '');

        $result = $this->patientService->updateInformation($request->all(), $request->id);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.patientsMgt.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('patients-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * delete the patient
     *
     * @param  Request $request
     * @return Response
     */
    public function delete(Request $request)
    {   
        // Save activity for deleted
        $patientData = $this->patientService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$patientData->first_name];
        $this->activityServie->logs('deleted', config('app.activityModules')["Patients"], '', '', $keyForAddOperation, $valueForAddOperation);

        $result = $this->patientService->delete($request);

        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.patientsMgt.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }
}
