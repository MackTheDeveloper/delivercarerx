<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Hospice;
use App\Models\User;
use App\Models\Pharmacy;
use App\Service\ActivityService;
use App\Service\FacilityService;
use App\Service\HospiceService;
use App\Service\RoleService;
use App\Service\UserService;
use App\Service\BranchService;
use App\Service\StateService;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use Session;
use Response;
use Illuminate\Support\Carbon;

class HospiceUserController extends Controller
{

    protected $userService;
    protected $roleService;
    protected $facilityService;
    protected $hospiceService;
    protected $branchService;
    protected $activityService;


    // /**
    //  * constructor for initialize Admin service
    //  *
    //  * @param HospiceService $hospiceService reference to hospiceService
    //  *
    //  */
    public function __construct(UserService $userService, RoleService $roleService, FacilityService $facilityService, HospiceService $hospiceService, BranchService $branchService, ActivityService $activityService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->facilityService = $facilityService;
        $this->hospiceService = $hospiceService;
        $this->branchService = $branchService;
        $this->activityService = $activityService;
    }


    /**
     * Listing of the user hospices
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        return view('admin.hospice-user.user-list');
    }

    public function list(Request $request)
    {
        $result = $this->userService->fetchHospiceUserListing($request);
        return Response::json($result);
    }

    /**
     * Show the form for add
     *
     * @param Request $request
     */
    public function add(Request $request)
    {
        $hospice = $this->hospiceService->getDropDownList();
        $roles = config('app.hospice_user_role');
        $timezone = config('app.timezones');
        return view('admin.hospice-user.user-add', compact('hospice', 'roles', 'timezone'));
    }

    /**
     * store the user hospices information
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$request->name, $request->email];
        $this->activityService->logs('added', config('app.activityModules')["HospiceUser"], '', config('app.activityModules')["HospiceUser"], $keyForAddOperation, $valueForAddOperation);

        $requestType = $request->get('submit');
        $result = $this->userService->addInformationHospice($request);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.hospiceUserMgt.created'),
                'alert-type' => 'success'
            );
            if ($requestType == 'save_and_new') {
                return redirect()->back()->with($notification);
            } else if ($requestType == 'save') {
                return redirect()->route('hospice-user-list')->with($notification);
            } else {
                return redirect()->back()->with($notification);
            }
        } else if ($result == 'email_not_valid') {
            $notification = array(
                'message' => config('message.hospiceUserMgt.existing_email_notification'),
                'alert-type' => 'error'
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
     * Edit form for hospice user information
     *
     * @param  $id
     */
    public function edit($id)
    {
        $model = User::findOrFail($id);
        $roles = config('app.hospice_user_role');
        $hospice = $this->hospiceService->getDropDownList();
        $facility = $this->facilityService->getDropDownList();
        $branches = $this->branchService->getDropDownList();
        return view('admin.hospice-user.user-edit', compact('model', 'roles', 'branches', 'hospice', 'facility', 'branches'));
    }

    /**
     * update the hospice user information
     *
     * @param Request $request
     */
    public function update(Request $request)
    {
        $model = $this->userService->fetchInformation($request->input('id'));
        $model->fill($request->input());
        $this->activityService->logs('updated', config('app.activityModules')["HospiceUser"], $model, '', '', '');

        $result = $this->userService->updateInformationHospice($request->all(), $request->input('id'));
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.hospiceUserMgt.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('hospice-user-list')->with($notification);
        } else if ($result == 'email_not_valid') {
            $notification = array(
                'message' => config('message.hospiceUserMgt.existing_email_notification'),
                'alert-type' => 'error'
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
     * delete the hospice user information
     *
     * @param Request $request
     */
    public function delete(Request $request)
    {
        $data = $this->userService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$data->name];
        $this->activityService->logs('deleted', config('app.activityModules')["HospiceUser"], '', config('app.activityModules')["HospiceUser"], $keyForAddOperation, $valueForAddOperation);

        $result = $this->userService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';

            $return['msg'] = config('message.hospiceUserMgt.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }

    /**
     * fetch states from country
     *
     * @param  $hospiceId
     * @return Response
     */
    public function fetchFacility($hospiceId)
    {
        return $this->facilityService->fetchFacilityService($hospiceId);
    }

    /**
     * fetch states from country
     *
     * @param  $facilityId
     * @return Response
     */
    public function fetchBranches($facilityId)
    {
        return $this->branchService->fetchBranchesService($facilityId);
    }
}
