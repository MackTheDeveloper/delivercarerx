<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\CountryService;
use App\Service\HospiceService;
use App\Service\StateService;
use App\Service\CustomerService;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;

class customerController extends Controller
{

    protected $customerService, $countryService, $stateService, $cityService;

    /**
     * constructor for initialize Admin service
     *
     * @param customerService $customerService reference to customerService
     * @param CountryService $countryService reference to countryService
     * @param StateService $stateService reference to stateService
     * @param CityService $cityService reference to cityService
     * 
     */
    public function __construct(customerService $customerService, CountryService $countryService, StateService $stateService, CityService $cityService)
    {
        $this->customerService = $customerService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
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
        return view('admin.customer.add', compact('countries'));
    }

    /**
     * 
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return view('admin.customer.list');
    }

    public function list(Request $request)
    {
        $result = $this->customerService->fetchListing($request);
        return Response::json($result);
    }

    /**
     * store the customer information
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $result = $this->customerService->addInformation($request->all());
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.customerMgt.created'),
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
     * Edit form for customer information
     *
     * @param  Request $request
     * @param  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $model = $this->customerService->fetchInformation($id);
            if ($model) {
                $countries = $this->countryService->getCountryList();
                $states = $this->stateService->getStateList($model->country_id);
                $cities = $this->cityService->getCityList($model->state_id);
                return view('admin.customer.edit', compact('countries', 'states', 'cities', 'model'));
            } else {
                abort('404');
            }
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * update the customer information
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $result = $this->customerService->updateInformation($request, $request->input('id'));
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.customerMgt.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('customer-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * delete the customer
     *
     * @param  Request $request
     * @return Response
     */
    public function delete(Request $request)
    {
        
        $result = $this->customerService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.customerMgt.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }
}
