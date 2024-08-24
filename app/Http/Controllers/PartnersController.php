<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partners;
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
use App\Service\PartnerService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\ActivityService;
use Illuminate\Support\Facades\Hash;

class PartnersController extends Controller
{

    protected $partnerService,$activityService;


    public function __construct(PartnerService $partnerService,ActivityService $activityService)
    {
        $this->partnerService = $partnerService;
        $this->activityService = $activityService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {
        return view('admin.partners.list');
    }

    public function list(Request $request)
    {
        return $result = $this->partnerService->fetchListing($request);
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
                $partners = Partners::all();
                return view('admin.partners.add');
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
        // Save activity for new hospice added
        // $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        // $valueForAddOperation = [$request->name, $request->code];
        // $this->activityService->logs('added', config('app.activityModules')["Partner"], '', config('app.activityModules')["Partner"], $keyForAddOperation, $valueForAddOperation);
        $result = $this->partnerService->addInformation($request);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.partners.created'),
                'alert-type' => 'success'
            );

            return redirect()->route('partners-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $id = decrypt($id);
            $data = $this->partnerService->fetchInformation($id);
            if ($data) {
                $partners = Partners::all();
                return view('admin.partners.edit', compact('partners','data'));
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
        $model = $this->partnerService->fetchInformation($id);
        // $model->fill($request->input());
        // $this->activityService->logs('updated', config('app.activityModules')["Partners"], $model, '', '', '');
            
        $result = $this->partnerService->updateInformation($request, $id);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.partners.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('partners-list')->with($notification);
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
        $partnersData = $this->partnerService->fetchInformation($request->id);
        // $keyForAddOperation = ['{PARAM}'];
        // $valueForAddOperation = [$facilityData->name];
        // $this->activityService->logs('deleted', config('app.activityModules')["Facilities"], '', '', $keyForAddOperation, $valueForAddOperation);

        $result = $this->partnerService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.partners.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }
}
