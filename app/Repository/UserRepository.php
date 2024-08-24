<?php

namespace App\Repository;

use App\Models\Branch;
use App\Models\Hospice;
use App\Models\NurseBranch;
use App\Models\User;
use App\Repository\ActivityRepository;
use App\Repository\EmailTemplatesRepository;
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\Validator;
use Auth;

class UserRepository
{
    protected $activityRepo;


    /**
     * @param ActivityRepository $activityRepo reference to activityRepo
     *
     */
    public function __construct(ActivityRepository $activityRepo)
    {
        $this->activityRepo = $activityRepo;
    }
    /**
     * Verify email form user table
     * @param array $attributes
     * @return object|array
     */
    public function verifyAdminEmail($attributes)
    {
        $user = User::where('email', $attributes['email'])->first();
        return $user;
    }
    /**
     * Fetch user information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return User::where('id', $id)->first();
    }
    public function fetchUser($id)
    {
        return User::select('first_name', 'last_name', 'email', 'phone', 'address1', 'address2', 'country_id', 'state_id', 'city_id', 'zipcode')->where('id', $id)->first();
    }
    public function fetchNurse($id)
    {
        return NurseBranch::where('id', $id)->first();
    }

    /**
     * Create hospice user
     * @param array $data
     * @return Response
     */
    public function createHospiceUser($data)
    {
        return User::create($data);
    }

    public function createUser($input)
    {
        $request_data = [
            'email' => 'required|email|unique:users,email',
            'gender' => 'required'
        ];
        $validator = Validator::make($input, $request_data);
        if ($validator->fails()) {
            return 'Required Feilds Missing!';
        }
        $model = new User();
        $model->name = $input['name'];
        $model->role_id = $input['role_id'];
        $model->email = $input['email'];
        $model->phone = $input['phone'];
        $model->password = Hash::make($input['password']);
        $model->timezone = $input['timezone'];
        $model->gender = $input['gender'];
        $model->is_active = $input['is_active'];
        $model->user_type = $input['user_type'];

        if (isset($input['pharmacy_id'])) {
            $model->pharmacy_id = implode(',', $input['pharmacy_id']);
        }
        if ($input['role_id'] == '1') {
            $model->pharmacy_id = NULL;
        }
        $model->save();
        try {
            $name = $input['name'];
            $email = $input['email'];
            $password = $input['password'];
            $data = ['NAME' => $name, 'EMAIL' => $email, 'PASSWORD' => $password];
            EmailTemplatesRepository::sendMail('create-new-user', $data);
        } catch (\Exception $e) {
            pre($e->getMessage());
        }
        return 'success';
    }
    public function createHospiceUsers($input)
    {
        $request_data = [
            'email' => 'required|email|unique:users,email',
        ];
        $validator = Validator::make($input, $request_data);
        if ($validator->fails()) {
            return 'email_not_valid';
        }
        $model = new User();
        $model->name = $input['name'];
        $model->hospice_user_role = $input['hospice_user_role'];
        $model->email = $input['email'];
        $model->phone = $input['phone'];
        $model->hospice_id = $input['hospice_id'];
        $model->facility_id = $input['facility_id'];
        $model->password = Hash::make($input['password']);
        $model->gender = $input['gender'];
        $model->is_active = $input['is_active'];
        $model->user_type = 2;
        if (isset($input['branch_id'])) {
            $model->branch_id = implode(',', $input['branch_id']);
        }

        $model->save();
        try {
            $name = $input['name'];
            $email = $input['email'];
            $password = $input['password'];
            $data = ['NAME' => $name, 'EMAIL' => $email, 'PASSWORD' => $password];
            EmailTemplatesRepository::sendMail('create-new-user', $data);
        } catch (\Exception $e) {
            pre($e->getMessage());
        }
        return 'success';
    }

    public function createHospiceNurse($input)
    {
        $request_data = [
            'email' => 'required|email|unique:users,email',
        ];
        $validator = Validator::make($input, $request_data);
        if ($validator->fails()) {
            return 'email_not_valid';
        }
        $model = new User();
        $model->name = $input['name'];
        $fullNameArr = explode(" ", $input['name']);
        if(COUNT($fullNameArr) == 2)
        {
            $model->first_name = $fullNameArr[0];
            $model->last_name = $fullNameArr[1];
        }
        elseif(COUNT($fullNameArr) == 1)
        {
            $model->first_name = $fullNameArr[0];
            $model->last_name = $fullNameArr[0];
        }
        else
        {
            $model->first_name = $fullNameArr[0];
            $model->last_name = $fullNameArr[1];
        }
        $model->hospice_user_role = 3;
        $model->user_type = 2;
        $model->hospice_id = Auth::user()->hospice_id;
        $model->email = $input['email'];
        $model->phone = $input['phone'];
        $model->password = Hash::make($input['password']);
        $model->gender = $input['gender'];
        $model->is_active = $input['is_active'];
        $model->user_type = $input['user_type'];
        if (isset($input['branch_id'])) {
            $model->branch_id = implode(',', $input['branch_id']);
        }
        $model->save();
        if (isset($input['branch_id'])) {
            foreach ($input['branch_id'] as $branch) {
                $branch = Branch::getBranchAndHospiceIds($branch);
                $nurseBranch = new NurseBranch();
                $nurseBranch->user_id = $model->id;
                $nurseBranch->hospice_id = $branch['hospice_id'];
                $nurseBranch->branch_id = $branch['branch_id'];
                $nurseBranch->facility_id = $branch['facility_id'];
                $nurseBranch->save();
            }
        }
        try {
            $name = $input['name'];
            $email = $input['email'];
            $password = $input['password'];
            $data = ['NAME' => $name, 'EMAIL' => $email, 'PASSWORD' => $password];
            EmailTemplatesRepository::sendMail('create-new-user', $data);

            //            // Save activity for new nurse
            //            $activityData['module_name'] = config('app.activityModules')["Nurse"];
            //            $activityData['performed_by'] = Auth::user()->id;
            //            $activityData['description'] = str_replace('{PARAM}', $input['name'], config('app.activityDescriptions')['Added']);
            //            $this->activityRepo->create($activityData);
        } catch (\Exception $e) {
            pre($e->getMessage());
        }
        return 'success';
    }

    /**
     * Update user information
     * @param array $data
     * @return Response
     */
    public function update($input, $id = null)
    {
        $model = User::find($id);
        $model->name = $input['name'];
        $model->role_id = $input['role_id'];
        $model->email = $input['email'];
        $model->phone = $input['phone'];
        $model->timezone = $input['timezone'];
        $model->gender = $input['gender'];
        $model->is_active = $input['is_active'];
        if (isset($input['pharmacy_id'])) {
            $model->pharmacy_id = implode(',', $input['pharmacy_id']);
        } else {
            $model->pharmacy_id = NULL;
        }
        if ($input['role_id'] == '1') {
            $model->pharmacy_id = NULL;
        }
        $model->update();
        return 'success';
    }


    /**
     * Update user information
     * @param array $data
     * @return Response
     */
    public function updateHospiceUsers($input, $id = null)
    {
        //  $request_data = [
        //     'email' => 'required|email|unique:users,email',
        // ];
        // $validator = Validator::make($input, $request_data);
        // if ($validator->fails()) {
        //     return 'email_not_valid';
        // }
        $model = User::find($id);
        $model->name = $input['name'];
        $model->hospice_user_role = $input['hospice_user_role'];
        $model->email = $input['email'];
        $model->phone = $input['phone'];
        $model->hospice_id = $input['hospice_id'];
        $model->facility_id = $input['facility_id'];
        $model->gender = $input['gender'];
        $model->is_active = $input['is_active'];
        if ($input['hospice_user_role'] == '1')
        {
            $model->facility_id = NULL;
            $model->branch_id = NULL;
        }
        else if ($input['hospice_user_role'] == '2')
        {
            $model->facility_id = $input['facility_id'];
            if (isset($input['branch_id'])) {
                $model->branch_id = implode(',', $input['branch_id']);
            }
        }
        $model->update();
        return 'success';
    }

    public function updateHospiceNurse($input, $id = null)
    {
        $model = User::find($id);
        $model->name = $input['name'];
        $model->email = $input['email'];
        $model->phone = $input['phone'];
        $model->timezone = $input['timezone'];
        $model->gender = $input['gender'];
        $model->is_active = $input['is_active'];
        if (isset($input['branch_id'])) {
            $model->branch_id = implode(',', $input['branch_id']);
        }
        $model->update();
        $existing_id = NurseBranch::getExistingIds($model->id);
        $branchIds = [];
        if($input['branch_id'])
        {
            foreach ($input['branch_id'] as $branch) {
                if (!in_array($branch, $existing_id)) {
                    $branchIds[] = $branch;
                }
            }
            foreach ($input['branch_id'] as $branch) {
                $branch = Branch::getBranchAndHospiceIds($branch);
                $nurseBranch = new NurseBranch();
                $nurseBranch->user_id = $model->id;
                $nurseBranch->hospice_id = $branch['hospice_id'];
                $nurseBranch->branch_id = $branch['branch_id'];
                $nurseBranch->facility_id = $branch['facility_id'];
                $nurseBranch->save();
            }
        }
        return 'success';
    }


    public function fetchUsers()
    {
        $users = User::getAllData();
        return $users;
    }
    /**
     * Fetch hospice users
     * @param $hospiceId
     * @return array
     */
    public function fetchHospiceUsers($hospiceId)
    {
        $hospiceUsers = User::select('profile_picture', 'name')->where('hospice_id', $hospiceId)->get()->toArray();
        return $hospiceUsers;
    }

    public function fetchPharmacyUsers($pharmacyId)
    {
        $pharmacyUsers = User::select("profile_picture", "name")
            ->whereRaw("FIND_IN_SET($pharmacyId, `pharmacy_id`)")
            ->where('user_type', 1)
            ->get()->toArray();


        return $pharmacyUsers;
    }

    public function storeNurseData($input)
    {
        if ($input['hospice_id']) {
            $existing_id = NurseBranch::getExistingIds($model->id);
            $branchIds = [];
            foreach ($input['branch_id'] as $branch) {
                if (!in_array($branch, $existing_id)) {
                    $branchIds[] = $branch;
                }
            }
            foreach ($input['nurse_id'] as $key => $value) {
                $branch = Branch::getBranchAndHospiceIds($input['hospice_id']);
                $nurseBranch = new NurseBranch();
                $nurseBranch->user_id = $value;
                $nurseBranch->hospice_id = $branch['hospice_id'];
                $nurseBranch->branch_id = $branch['branch_id'];
                $nurseBranch->facility_id = $branch['facility_id'];
                $nurseBranch->save();
            }
        }
        return 'success';
    }
    /**
    /**
     * Get all userIds of the hospice
     * @return array
     */
    public function getUserIdsOfHospice()
    {
        $hospiceId = Auth::user()->hospice_id;
        return User::where('hospice_id', $hospiceId)->pluck('id')->toArray();
    }

    public function createHospiceAssignNurse($input)
    {
        $existingNurseData = NurseBranch::where('branch_id', $input['hospice_id'])->whereNull('deleted_at')->get()->toArray();
        $nurseData = [];
        foreach ($existingNurseData as $key => $value) {
            $nurseData[$key] = $value['user_id'];
        }

        $branch = Branch::getBranchAndHospiceIds($input['hospice_id']);
        if (!empty($input['nurse_id'])) {
            foreach ($input['nurse_id'] as $key => $value) {
                if (!in_array($value, $nurseData)) {
                    $nurseBranch = new NurseBranch();
                    $nurseBranch->user_id = $value;
                    $nurseBranch->hospice_id = $branch['hospice_id'];
                    $nurseBranch->branch_id = $branch['branch_id'];
                    $nurseBranch->facility_id = $branch['facility_id'];
                    $nurseBranch->save();
                }
            }
        } else {
            $input['nurse_id'] = [];
        }
        foreach ($nurseData as $key => $value) {
            if (!in_array($value, $input['nurse_id'])) {
                NurseBranch::deleteIfExistence($value, $input['hospice_id']);
            }
        }

        return 'success';
    }
    public function deleteUser($hospiceId)
    {
        if ($hospiceId) {
            $userIds =  User::where('hospice_id', $hospiceId)->pluck('id')->toArray();
            foreach ($userIds as $key => $value) {
                User::where('id', $value)->delete();
            }
        }
        return 'success';
    }
}
