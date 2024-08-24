<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\ActivityService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\UserService;
use App\Service\CountryService;
use App\Service\HospiceService;
use App\Service\StateService;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;

use App\Imports\HospiceImport;
use Excel;


class HospiceController extends Controller
{

    protected $hospiceService, $countryService, $stateService, $cityService, $activityServie,$userService;

    /**
     * constructor for initialize Admin service
     *
     * @param HospiceService $hospiceService reference to hospiceService
     * @param CountryService $countryService reference to countryService
     * @param StateService $stateService reference to stateService
     * @param CityService $cityService reference to cityService
     * @param ActivityService $activityServie reference to activityServie
     *
     */
    public function __construct(HospiceService $hospiceService, CountryService $countryService, StateService $stateService, CityService $cityService, ActivityService $activityServie,UserService $userService)
    {
        $this->hospiceService = $hospiceService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
        $this->activityServie = $activityServie;
        $this->userService = $userService;
    }

    /**
     * Show the form for login
     *
     * @param  Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $countries = $this->countryService->getCountryList();
        return view('admin.hospice.hospice-add', compact('countries'));
    }

    /**
     * Listing of the hospices
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return view('admin.hospice.hospice-list');
    }

    public function list(Request $request)
    {
        $result = $this->hospiceService->fetchListing($request);
        return Response::json($result);
    }

    /**
     * store the hospice information
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $result = $this->hospiceService->addInformation($request);

        // Save activity for new hospice added
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$request->name, $request->code];
        $this->activityServie->logs('added', config('app.activityModules')["Hospice"], '', config('app.activityModules')["Hospice"], $keyForAddOperation, $valueForAddOperation);

        if ($result == 'success') {
            $notification = array(
                'message' => config('message.hospiceMgt.created'),
                'alert-type' => 'success'
            );
            return redirect()->route('hospice-list')->with($notification);
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
            $model = $this->hospiceService->fetchInformation($id);
            if ($model) {
                $countries = $this->countryService->getCountryList();
                $states = $this->stateService->getStateList($model->country_id);
                $cities = $this->cityService->getCityList($model->state_id);
                return view('admin.hospice.hospice-edit', compact('countries', 'states', 'cities', 'model'));
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
        $model = $this->hospiceService->fetchInformation($request->input('id'));
        $model->fill($request->input());
        $this->activityServie->logs('updated', config('app.activityModules')["Hospice"], $model, '', '', '');

        /* $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$request->name];
        $this->activityServie->logs('updated', config('app.activityModules')["Hospice"], '', '', $keyForAddOperation, $valueForAddOperation); */

        $result = $this->hospiceService->updateInformation($request, $request->input('id'));
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.hospiceMgt.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('hospice-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * delete the hospice
     *
     * @param  Request $request
     * @return Response
     */
    public function delete(Request $request)
    {
        // Save activity for deleted
        $hospiceData = $this->hospiceService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$hospiceData->name];
        $this->activityServie->logs('deleted', config('app.activityModules')["Hospice"], '', '', $keyForAddOperation, $valueForAddOperation);
        $result = $this->hospiceService->delete($request);
        if ($result == 'success') {
            $user = $this->userService->deleteUserByHospiceId($request->id);
            $return['status'] = 'true';
            $return['msg'] = config('message.hospiceMgt.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }

    public function importHospice(Request $request)
    {
        return view('admin.import.import-hospice');
    }

    public function uploadHospice(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $import = new HospiceImport();
                $extension = $request->file('file')->extension();
                if ($extension == "xlsx") {
                    Excel::import($import, $request->file('file'), null, \Maatwebsite\Excel\Excel::XLSX);
                } elseif ($extension == "xls") {
                    Excel::import($import, $request->file('file'));
                }
                $collection = $import->getCommon();
                //dd($collection);
                $numerOfSuccess = 0;
                $numerOfFailed = 0;
                $somethingWentWrong = 0;
                $columnMismatch = 0;
                foreach ($collection as $row) {
                    if ($row == 'Column Mismatch') {
                        $columnMismatch = 1;
                        break;
                    }

                    $hospiceData['code'] = trim($row[0]);
                    $hospiceData['name'] = trim($row[1]);
                    $hospiceData['address_1'] = trim($row[2]);
                    $hospiceData['address_2'] = trim($row[3]);

                    // Fetch State ID from state name
                    if($row['5']!='')
                    {
                        $stateData = $this->stateService->fetchStateDataFromName($row['5']);
                    if (!$stateData) {
                        $newStateData = $this->stateService->addNewState($row['5']);
                        $hospiceData['state_id'] = $newStateData->id;
                    } else {
                        $hospiceData['state_id'] = $stateData->id;
                    }
                    }else{
                        $hospiceData['state_id'] = null;
                    }


                    // Fetch City ID from city name
                    if($row['4']!='')
                    {
                    $cityData = $this->cityService->fetchCityDataFromName($hospiceData['state_id'], $row['4']);
                    if (!$cityData) {
                        $cityData['state_id'] = $hospiceData['state_id'];
                        $cityData['name'] = $row['4'];
                        $newCityData = $this->cityService->addNewCity($cityData);
                        $hospiceData['city_id'] = $newCityData->id;
                    } else {
                        $hospiceData['city_id'] = $cityData->id;
                    }
                }else{
                    $hospiceData['city_id'] = null;
                }


                    $hospiceData['zipcode'] = trim($row[6]);
                    $hospiceData['email'] = $row[7];

                    $hospiceData['created_by'] = Auth::user()->id;

                    $response = $this->hospiceService->uploadInformation($hospiceData);

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

        //audit trails for import hospice
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$numerOfSuccess, $numerOfFailed];
        $this->activityServie->logs('import', config('app.activityModules')["Import-Hospice"], '', config('app.activityModules')["Import-Hospice"], $keyForAddOperation, $valueForAddOperation);
    }
    public function hospiceByIdZero()
    {
        $result = $this->hospiceService->hospiceByIdZeroService();
    }
}
