<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\CountryService;
use App\Service\HospiceService;
use App\Service\StateService;
use App\Service\ActivityService;
use App\Service\PharmacyService;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;

class PharmacyController extends Controller
{

    protected $pharmacyService, $countryService, $stateService, $cityService, $activityServie;

    /**
     * constructor for initialize Admin service
     *
     * @param PharmacyService $pharmacyService reference to pharmacyService
     * @param CountryService $countryService reference to countryService
     * @param StateService $stateService reference to stateService
     * @param CityService $cityService reference to cityService
     * 
     */
    public function __construct(PharmacyService $pharmacyService, CountryService $countryService, StateService $stateService, CityService $cityService, ActivityService $activityServie)
    {
        $this->pharmacyService = $pharmacyService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
        $this->activityServie = $activityServie;
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
        return view('admin.pharmacy.pharmacy-add', compact('countries'));
    }

    /**
     * Listing of the pharmacies
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return view('admin.pharmacy.pharmacy-list');
    }

    public function list(Request $request)
    {
        $result = $this->pharmacyService->fetchListing($request);
        return Response::json($result);
    }

    /**
     * store the pharmacy information
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $result = $this->pharmacyService->addInformation($request->all());

          // Save activity for new pharmacy added
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$request->name];
        $this->activityServie->logs('added', config('app.activityModules')["Pharmacy"], '', config('app.activityModules')["Pharmacy"], $keyForAddOperation, $valueForAddOperation);

        if ($result == 'success') {
            $notification = array(
                'message' => config('message.pharmacyMgt.created'),
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * Edit form for pharmacy information
     *
     * @param  Request $request
     * @param  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $model = $this->pharmacyService->fetchInformation($id);
            if ($model) {
                return view('admin.partners.edit', compact('model'));
            } else {
                abort('404');
            }
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * update the pharmacy information
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        // Save activities for updated information
        $model = $this->pharmacyService->fetchInformation($request->input('id'));
        $model->fill($request->input());
        $this->activityServie->logs('updated', config('app.activityModules')["Pharmacy"], $model, '', '', '');

        $result = $this->pharmacyService->updateInformation($request, $request->input('id'));
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.pharmacyMgt.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('pharmacy-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * delete the pharmacy
     *
     * @param  Request $request
     * @return Response
     */
    public function delete(Request $request)
    {
        // Save activity for deleted
        $pharmacyData = $this->pharmacyService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$pharmacyData->name];
        $this->activityServie->logs('deleted', config('app.activityModules')["Pharmacy"], '', '', $keyForAddOperation, $valueForAddOperation);

        $result = $this->pharmacyService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.pharmacyMgt.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }
}
