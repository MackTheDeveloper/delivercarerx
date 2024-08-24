<?php

namespace App\Http\Controllers;

use App\Imports\DeliverImport;
use App\Models\User;
use App\Models\Pharmacy;
use App\Service\ActivityService;
use App\Service\RoleService;
use App\Service\UserService;
use App\Service\PharmacyService;
use App\Service\StateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\Facades\Hash;
use Session;
use Response;

use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use App\Repository\EmailTemplatesRepository;



use Illuminate\Support\Carbon;

class UserController extends Controller
{

    protected $userService;
    protected $pharmacyService;
    protected $roleService;
    protected $activityService;

    // /**
    //  * constructor for initialize Admin service
    //  *
    //  * @param HospiceService $hospiceService reference to hospiceService
    //  *
    //  */
    public function __construct(UserService $userService, PharmacyService $pharmacyService, RoleService $roleService, ActivityService $activityService)
    {
        $this->userService = $userService;
        $this->pharmacyService = $pharmacyService;
        $this->roleService = $roleService;
        $this->activityService = $activityService;
    }


    /**
     * Listing of the Users
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        return view('admin.user.user-list');
    }

    /**
     * import the nurses
     *
     * @param Request $request
     */
    public function import()
    {
        return view('admin.import.deliver');
    }

    /**
     * import the nurses store function
     *
     * @param Request $request
     * @return Response
     */


        public function importData(Request $request)
    {
        $validator = Validator::make(['file' => request()->file('file'), 'extension' => strtolower($request->file->getClientOriginalExtension())], ['file' => 'required', 'extension' => 'required|in:csv,xlsx,xls,ods']);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
        $import = new DeliverImport;
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


                $user = new \App\Models\User;

                $user->first_name = $row[0];
                $user->last_name = $row[1];
                $user->name = $row[2];
                $user->user_logon_name = $row[3];
                $user->street = $row[4];
                $user->email = $row[5];
                $user->city_id = $row[6];
                $user->state_id = $row[7];
                $user->zipcode = $row[8];
                $user->country_id = $row[9];
                $user->job_title = $row[10];
                $user->department = $row[11];
                $user->company = $row[12];
                $user->manager = $row[13];
                $user->description = $row[14];
                $user->office = $row[15];
                $user->phone = $row[16];
                $user->initials  = $row[17];
                $user->role_id = 3;
                $user->user_type = 1;

                $user->save();

                    $name = $row[0] . ' ' . $row[1];
                    $email = $row[5];
                    $encryptId = encrypt($user->id);
                    $link = route('show-set-password', $encryptId);
                    $data = ['NAME' => $name, 'EMAIL' => $email, 'LINK' => $link];
                    $val = EmailTemplatesRepository::sendMail('set-password', $data);

                     $scounter++;

        }
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$scounter, $fcounter];
        $this->activityService->logs('import', config('app.activityModules')["Import-DeliverCareX Users"], '', config('app.activityModules')["Import-DeliverCareX Users"], $keyForAddOperation, $valueForAddOperation);

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


    /**
     * Listing of the Users
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $result = $this->userService->fetchListing($request);
        return Response::json($result);
    }

    /**
     * Show the form for add
     *
     * @param Request $request
     */
    public function add(Request $request)
    {
        $pharmacy = $this->pharmacyService->fetchList();
        $roles = $this->roleService->dropdown();
        $timezone = config('app.timezones');
        return view('admin.user.user-add', compact('pharmacy', 'roles', 'timezone'));
    }

    /**
     * store the user information
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $keyForAddOperation = ['{PARAM}', '{PARAM1}'];
        $valueForAddOperation = [$request->name, $request->email];
        $this->activityService->logs('added', config('app.activityModules')["User"], '', config('app.activityModules')["User"], $keyForAddOperation, $valueForAddOperation);

        $requestType = $request->get('submit');
        $result = $this->userService->addInformation($request);
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.userMgt.created'),
                'alert-type' => 'success'
            );
            if ($requestType == 'save_and_new') {
                return redirect()->back()->with($notification);
            } else if ($requestType == 'save') {
                return redirect()->route('user-list')->with($notification);
            } else {
                return redirect()->back()->with($notification);
            }
        } else if ($result == 'email_not_valid') {
            $notification = array(
                'message' => config('message.userMgt.existing_email_notification'),
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
     * Edit form for user information
     *
     * @param Request $request
     * @param  $id
     */
    public function edit($id)
    {
        $model = User::findOrFail($id);
        $pharmacy = $this->pharmacyService->fetchList();
        $roles = $this->roleService->dropdown();
        $timezone = config('app.timezones');
        return view('admin.user.user-edit', compact('model', 'pharmacy', 'roles', 'timezone'));
    }

    /**
     * update the user information
     *
     * @param Request $request
     */
    public function update(Request $request)
    {
        $model = $this->userService->fetchInformation($request->input('id'));
        $model->fill($request->input());
        $this->activityService->logs('updated', config('app.activityModules')["User"], $model, '', '', '');

        $result = $this->userService->updateInformation($request->all(), $request->input('id'));
        if ($result == 'success') {
            $notification = array(
                'message' => config('message.userMgt.updated'),
                'alert-type' => 'success'
            );
            return redirect()->route('user-list')->with($notification);
        } else {
            $notification = array(
                'message' => config('message.somethingWentWrong'),
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }

    /**
     * delete the user
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request)
    {

        // $model = User::where('id', $request->id)->first();
        $hospiceData = $this->userService->fetchInformation($request->id);
        $keyForAddOperation = ['{PARAM}'];
        $valueForAddOperation = [$hospiceData->name];
        $this->activityService->logs('deleted', config('app.activityModules')["User"], '', '', $keyForAddOperation, $valueForAddOperation);

        $result = $this->userService->delete($request);
        if ($result == 'success') {
            $return['status'] = 'true';
            $return['msg'] = config('message.userMgt.deleted');
        } else {
            $return['status'] = 'false';
            $return['msg'] = config('message.somethingWentWrong');
        }
        return $return;
    }
}
