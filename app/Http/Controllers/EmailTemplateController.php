<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplates;
use App\Models\EmailTemplatesCc;
use Auth;
use Session;
use DB;
use Illuminate\Support\Facades\Validator;

use App\Service\EmailTemplateService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\CountryService;
use App\Service\HospiceService;
use App\Service\StateService;
use App\Service\ActivityService;

class EmailTemplateController extends Controller
{

    protected $HospiceService,$emailTempService, $countryService, $stateService, $cityService,$activityServie;


    public function __construct(EmailTemplateService $emailTempService, HospiceService $hospiceService, CountryService $countryService, StateService $stateService, CityService $cityService,ActivityService $activityServie)
    {
        $this->emailTempService = $emailTempService;
        $this->countryService = $countryService;
        $this->stateService = $stateService;
        $this->cityService = $cityService;
        $this->hospiceService = $hospiceService;
        $this->activityServie = $activityServie;
    }

    

    public function index(Request $request)
    {
        return view('admin.email-template.list');
    }

    public function list(Request $request)
    {
       return $result = $this->emailTempService->fetchListing($request);
        return Response::json($result);
    }

    public function create()
    {
        try {
            if (Auth::check()) {
                
                return view('admin.email-template.add');
            }
                
            return view('admin.login');
        } catch (Exception $e) {
            return $e;
        }
    }

    public function store(Request $request)
    {
        $result = $this->emailTempService->addInformation($request);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.AuthMessages.emailTemplateSuccess'),
                'alert-type' => 'success'
            );
            // Save activity for new email template added
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$request->title, $request->title];
        $this->activityServie->logs('added', config('app.activityModules')["Email-Template"], '', config('app.activityModules')["Email-Template"], $keyForAddOperation, $valueForAddOperation);
            return redirect()->back()->with($notification);
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
            $data = $this->emailTempService->fetchInformation($id);
            if ($data) {
                return view('admin.email-template.edit', compact('data'));
            } else {
                abort('404');
            }
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * update the email-template information
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request,$id)
    {
         $id = decrypt($id);
         $request->is_active = $request->status;

         // Save activities for updated information
        $model = $this->emailTempService->fetchInformation($id);
        $model->fill($request->input());
        $this->activityServie->logs('updated', config('app.activityModules')["Email-Template"], $model, '', '', '');       

        $result = $this->emailTempService->updateInformation($request, $id);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.email-templateMgt.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('email-template-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    public function delete(Request $request)
    {

        // Save activity for deleted
        $emailTempData = $this->emailTempService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$emailTempData->title];
        $this->activityServie->logs('deleted', config('app.activityModules')["Email-Template"], '', config('app.activityModules')["Email-Template"], $keyForAddOperation, $valueForAddOperation);
        
        $result = $this->emailTempService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.AuthMessages.emailTemplateDelete');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }
}
