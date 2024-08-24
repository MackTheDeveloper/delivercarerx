<?php 

namespace App\Http\Controllers;

ini_set('max_execution_time', 0);
set_time_limit(3600);

use App\Models\Facility;
use App\Models\Hospice;
use App\Models\User;
use App\Models\NurseBranch;
use App\Models\Pharmacy;
use App\Service\FacilityService;
use App\Service\HospiceService;
use App\Service\RoleService;
use App\Service\UserService;
use App\Service\BranchService;
use App\Service\ActivityService;
use App\Service\StateService;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Hash;
use Session;
use Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Branch;
use App\Repository\EmailTemplatesRepository;

class NurseController extends Controller
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
    public function __construct(UserService $userService, RoleService $roleService, FacilityService $facilityService, HospiceService $hospiceService, BranchService $branchService,ActivityService $activityService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->facilityService = $facilityService;
        $this->hospiceService = $hospiceService;
        $this->branchService = $branchService;
        $this->activityService = $activityService;
    }


    /**
     * Listing of the nurse
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        return view('admin.nurse.user-list');
    }

    /**
     * Listing of the nurse
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $result = $this->userService->fetchNurseListing($request);
        return Response::json($result);
    }

    /**
     * Show the form for add
     *
     * @param Request $request
     */
    public function add(Request $request)
    {
        $branch = $this->branchService->getDropDownListBranchAndHospice();
        $timezone = config('app.timezones');
        return view('admin.nurse.user-add', compact('branch', 'timezone'));
    }

    /**
     * store the nurse information
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $requestType = $request->get('submit');
        $result = $this->userService->addInformationNurse($request);

        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$request->name, $request->email];
        $this->activityService->logs('added', config('app.activityModules')["Nurse"], '', config('app.activityModules')["Nurse"], $keyForAddOperation, $valueForAddOperation);

        if ($result == 'success') {
            $notification = array(
                'message' => config('message.nurseMgt.created'),
                'alert-type' => 'success'
            );
            if ($requestType == 'save_and_new') {
                return redirect()->back()->with($notification);
            } else if ($requestType == 'save') {
                return redirect()->route('nurse-user-list')->with($notification);
            } else {
                return redirect()->back()->with($notification);
            }
        }else if ($result == 'email_not_valid')
        {
            $notification = array(
                'message' => config('message.nurseMgt.existing_email_notification'),
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
     * Edit form for nurse information
     *
     * @param  $id
     */
    public function edit($id)
    {
        $model = User::findOrFail($id);
        $timezone = config('app.timezones');
        $branch = $this->branchService->getDropDownListBranchAndHospice();
        return view('admin.nurse.user-edit', compact('model','timezone','branch'));
    }

    /**
     * update the nurse information
     *
     * @param Request $request
     */
    public function update(Request $request)
    {
        // Save activities for updated information
        $model = $this->userService->fetchInformation($request->input('id'));
        $model->fill($request->input());
        $this->activityService->logs('updated', config('app.activityModules')["Nurse"], $model, '', '', '');

        $result = $this->userService->updateInformationNurse($request->all(), $request->input('id'));
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.nurseMgt.updated'),
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
     * delete the nurse
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request)
    {

        // $model = User::where('id', $request->id)->first();
        $data = $this->userService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$data->name];
        $this->activityService->logs('deleted', config('app.activityModules')["Nurse"], '', '', $keyForAddOperation, $valueForAddOperation);

        $result = $this->userService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.nurseMgt.deleted');
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


    //assign nurses
    /**
     * assign the nurse
     *
     * @param Request $request
     */
    public function assignNurse(Request $request)
    {
        $branch = $this->branchService->getDropDownListBranchAndHospice();
        return view('admin.nurse.assign-nurse',compact('branch'));
    }

    /**
     * get nurse data
     *
     * @param Request $request
     */
    public function getNurseData(Request $request)
    {
        $input = $request->all();
        $model = User::select('id', 'name')->where('hospice_user_role', 3)->whereNull('deleted_at')->get();
        $userIds = [];
        foreach ($model as $key => $value) {
            $userIds[$value['id']] = $value['name'];
        }

        $userIdsFinal = [];
        $nurse = NurseBranch::getNurseData($input['hospiceId']);
        foreach($userIds as $key => $value)
        {
            $userIdsFinal[$key]['id'] =  $key;
            $userIdsFinal[$key]['name'] =  $value;
                if (in_array($key,$nurse)) {
                        $userIdsFinal[$key]['selected'] =  'selected';
                }
                else
                {
                    $userIdsFinal[$key]['selected'] =  '';
                }

        }
        // dd($userIdsFinal);
        return $userIdsFinal;
    }

    public function updateAssignNurse(Request $request)
    {
        $requestType = $request->get('submit');
        $result = $this->userService->addInformationAssignNurse($request);

        // Save activities for updated information
        // $model = $this->userService->fetchInformation($request->input('id'));
        // $model->fill($request->input());
        // $this->activityService->logs('updated', config('app.activityModules')["Assign-Nurse"], $model, '', '', '');

          if ($result == 'success') {
            $notification = array(
                'message' => config('message.userMgt.updated'),
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
     * import the nurses
     *
     * @param Request $request
     */
    public function import()
    {
        return view('admin.nurse.import-nurses');
    }

    /**
     * import the nurses store function
     *
     * @param Request $request
     * @return Response
     */
    public function importNurse(Request $request)
    {
        $validator = Validator::make(['file' => request()->file('file'), 'extension' => strtolower($request->file->getClientOriginalExtension())], ['file' => 'required', 'extension' => 'required|in:csv,xlsx,xls,ods']);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        $import = new UsersImport;
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
            $email_arr = \App\Models\User::select('*')->pluck('email')->toArray();
            $flag = 'true';
            if (($row[0] == "" || $row[1] == "" || $row[2] == "" || $row[3] == "" || $row[4] == "" || $row[5] == "" || $row[6] == "")) {
                $flag = 'false';
                $errors['misMatch'][] = "Record is incomplete for Row - " . $counter . ". Please try again.";
            }
            if (in_array($row[2], $email_arr)) {
                $flag = 'false';
                $errors['emailErr'][] = $row[2] . " is already exist. Please use different email.";
            }
            if ($flag == 'true') {
                $user = new \App\Models\User;
                $user->first_name = $row[0];
                $user->last_name = $row[1];
                $user->name = $row[0] . " " . $row[1];
                $user->email = $row[2];
                $user->phone = $row[3];
                $user->branch_id = Branch::getIdsByBranchCode($row[4]);
                $user->import_admin = $row[5];
                $user->sms = $row[6];
                if ($row[5] == 'Yes' || $row[5] == 'YES' || $row[5] == '1') {
                    $user->hospice_user_role = 2;
                } else {
                    $user->hospice_user_role = 3;
                }
                $user->is_active = 1;
                $user->user_type = 2;
                $user->hospice_id = Auth::user()->hospice_id;

                if ($user->save()) {
                    if (isset($row[4])) {
                        foreach (explode(',', $row[4]) as $branch) {
                            $branch = Branch::getBranchAndHospiceIdsWithCode($branch);
                            $nurseBranch = new NurseBranch();
                            $nurseBranch->user_id = $user->id;
                            if($branch)
                            {
                                $nurseBranch->hospice_id = $branch['hospice_id'];
                                $nurseBranch->branch_id = $branch['branch_id'];
                                $nurseBranch->facility_id = $branch['facility_id'];
                            }
                            $nurseBranch->save();
                        }
                    }
                    $name = $row[0] . ' ' . $row[1];
                    $email = $row[2];
                    $encryptId = encrypt($user->id);
                    $link = route('show-set-password', $encryptId);
                    $data = ['NAME' => $name, 'EMAIL' => $email, 'LINK' => $link];
                    $val = EmailTemplatesRepository::sendMail('set-password', $data);

                }
                $scounter++;
            } else {
                $fcounter++;
            }
        }
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$scounter, $fcounter];
        $this->activityService->logs('import', config('app.activityModules')["Nurse"], '', config('app.activityModules')["Nurse"], $keyForAddOperation, $valueForAddOperation);

        if (!empty($errors['misMatch'])) {
            $countMissMatch = count($errors['misMatch']);
            $columnMismatchText = $errors['misMatch'];
        }
        if (!empty($errors['emailErr'])) {
            $countEmailExist = count($errors['emailErr']);
            $emailIdsText = $errors['emailErr'];
        }
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
}
