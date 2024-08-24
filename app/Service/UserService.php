<?php

namespace App\Service;

use App\Models\Facility;
use App\Models\Hospice;
use App\Models\NurseBranch;
use App\Models\Pharmacy;
use App\Models\Branch;
use App\Models\User;
use App\Repository\AdminRepository;
use App\Repository\HospiceRepository;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use Hash;
use PDO;
use Str;
use Auth;
use DB;


class UserService
{

    protected $hospiceRepo, $userRepo, $activityRepo;


    /**
     * @param HospiceRepository $hospiceRepo reference to hospiceRepo
     *
     */
    public function __construct(HospiceRepository $hospiceRepo, UserRepository $userRepo, ActivityRepository $activityRepo)
    {
        $this->hospiceRepo = $hospiceRepo;
        $this->userRepo = $userRepo;
        $this->activityRepo = $activityRepo;
    }


    /**
     * Add user information
     * @param object $request
     */
    public function addInformation($request)
    {
        $data = $request->all();
        try {
            $response = $this->userRepo->createUser($data);
            return $response;
        } catch (Exception $e) {
            return 'error';
        }
    }

    /**
     * Add hospice user information
     * @param object $request
     */
    public function addInformationHospice($request)
    {
        $data = $request->all();
        try {
            $response = $this->userRepo->createHospiceUsers($data);
            return $response;
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function addInformationNurse($request)
    {
        $data = $request->all();
        try {
            $response = $this->userRepo->createHospiceNurse($data);
            return $response;
        } catch (Exception $e) {
            return 'error';
        }
    }

    /**
 * Update hospice information
     * @param object $request
     */
    public function updateInformation($request, $id)
    {
        try {
            $response = $this->userRepo->update($request, $id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /**
     * Update hospice information
     * @param object $request
     */
    public function updateInformationHospice($request, $id)
    {
        try {
            $response = $this->userRepo->updateHospiceUsers($request, $id);
            return $response;
        } catch (Exception $e) {
            return 'error';
        }
    }

    /**
     * Update hospice information
     * @param object $request
     */
    public function updateInformationNurse($request, $id)
    {
        try {
            $response = $this->userRepo->updateHospiceNurse($request, $id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /**
     * Fetch hospice information
     * @param $id
     */
    public function fetchInformation($id)
    {
        return $this->userRepo->fetch($id);
    }

    public function fetchNurseInformation($id)
    {
        return $this->userRepo->fetchNurse($id);
    }

    public function fetchUserInformation($id)
    {
        return $this->userRepo->fetchUser($id);
    }


    /**
     * Add hospice information
     * @param object $request
     */
    public function fetchListing($request)
    {
        $req = $request->all();
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        $orderby = ['name', 'role_id', 'email', 'phone', 'pharmacy_id', 'is_active', 'created_at'];


        $total = User::selectRaw('count(*) as total')->whereNull('deleted_at')->where('user_type', 1)->first();
        $query = User::select('users.*')->where('user_type', 1)->whereNull('deleted_at');
        $filteredq = User::selectRaw('count(*) as total')->where('user_type', 1)->whereNull('deleted_at');
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where('name', 'like', '%' . $search . '%')->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")->orWhere('email', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where('name', 'like', '%' . $search . '%')->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")->orWhere('email', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }
        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();
        $data = [];
        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'user_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'user_delete');
        foreach ($query as $key => $value) {

            $action = '';
            $editUrl = route('edit-user', $value->id);
            $isEdit = $isEditable ? '<a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>' : '';
            $isDelete = $isDeletable ? '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>' : '';

            $actionInner = '';
            if ($isEdit || $isDelete)
                $actionInner = '<div class="dropdown-menu dropdown-menu-right">' . $isEdit . $isDelete . '</div';

            $action = '<div class="dropdown">
              <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>'.$actionInner.'</div>';
            $status = $value['is_active'] == 1 ? 'Active' : 'Inactive';
            $statusClass = $value['is_active'] == 1 ? 'success' : 'danger';
            $statusHtml = '<i class="bx bxs-circle ' . $statusClass . ' font-small-1 mr-50"></i>
            <span>' . $status . '</span>';
            if ($value['name']) {
                $name = $value['name'];
            } else {
                $name = $value['first_name'] . ' ' . $value['last_name'];
            }
            if ($value['role_id'] == 1) {
                $role_name = 'Site Admin';
            } else if ($value['role_id'] == 2) {
                $role_name = 'Pharmacy Admin';
            } else {
                $role_name = 'User';
            }
            $email = '';
            if (!empty($value['email'])) {
                $email = '<a href = "mailto:' . $value['email'] . '">' . $value['email'] . '</a>';
            }
            $phone = '';
            if (!empty($value['phone'])) {
                $phone = '<a href = "tel:' . $value['phone'] . '">' . $value['phone'] . '</a>';
            }
            $storeArray = Pharmacy::getNameById($value['pharmacy_id']);
            $storeData = ($storeArray[0] != null) ? strtoupper('[ ' . implode(', ', $storeArray) . ' ]') : '_';
            $data[] = [$name, $role_name, $email, $phone, $storeData, $statusHtml, getFormatedDate($value['created_at'], 'm/d/Y'), $action];
        }
        return array(
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

    /**
     * Add hospice user information
     * @param object $request
     */
    public function fetchHospiceUserListing($request)
    {
        $req = $request->all();
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        $orderby = ['name', 'role_id', 'hospice_id','facility_id','branch_id','email', 'phone', 'is_active', 'created_at'];

        $total = User::selectRaw('count(*) as total')->where('user_type', 2)->whereIn('hospice_user_role', [1, 2])->whereNull('deleted_at');
        $query = User::select('users.*')->where('user_type', 2)->whereIn('hospice_user_role', [1, 2])->whereNull('deleted_at');
        $filteredq = User::selectRaw('count(*) as total');
        if (Auth::user()->user_type == 2) {
            $query = $query->where('hospice_id', Auth::user()->hospice_id);
            $filteredq = $filteredq->where('hospice_id', Auth::user()->hospice_id);
            $total = $total->where('hospice_id', Auth::user()->hospice_id);
        }
        $total = $total->first();
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where('name', 'like', '%' . $search . '%')->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%");
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where('name', 'like', '%' . $search . '%')->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%");
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }
        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();
        $data = [];
        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_user_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_user_delete');
        foreach ($query as $key => $value) {
            $action = '';
            $editUrl = route('hospice-edit-user', $value->id);
            $isEdit = $isEditable ? '<a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>' : '';
            $isDelete = $isDeletable ? '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>' : '';

            $actionInner = '';
            if ($isEdit || $isDelete)
                $actionInner = '<div class="dropdown-menu dropdown-menu-right">' . $isEdit . $isDelete . '</div';

            $action = '<div class="dropdown">
              <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
              <div class="dropdown-menu dropdown-menu-right">
                '.$actionInner.'
              </div>
            </div>';
            $status = $value['is_active'] == 1 ? 'Active' : 'Inactive';
            $statusClass = $value['is_active'] == 1 ? 'success' : 'danger';
            $statusHtml = '<i class="bx bxs-circle ' . $statusClass . ' font-small-1 mr-50"></i>
            <span>' . $status . '</span>';
            if ($value['name']) {
                $name = $value['name'];
            } else {
                $name = $value['first_name'] . ' ' . $value['last_name'];
            }
            if ($value['role_id'] == 1) {
                $role_name = 'Site Admin';
            } else if ($value['role_id'] == 2) {
                $role_name = 'Pharmacy Admin';
            } else {
                $role_name = 'User';
            }
            $roleValue = '';
            if ($value['hospice_user_role'] == 1) {
                $roleValue = '<td class="text-success">Admin</td>';
            }
            if ($value['hospice_user_role'] == 2) {
                $roleValue = '<td class="text-warning">Branch Admin</td>';
            }
            $hospiceData = Hospice::getHospiceData($value->hospice_id);
            $facilityData = Facility::getFacilityData($value->facility_id);
            $branchData = Branch::getBranchData(explode(',',$value->branch_id));
            $data[] = [$name, $roleValue, $hospiceData, $facilityData['name'] ?? '-',$branchData ?? '-', $value['email'], $value['phone'], $statusHtml, getFormatedDate($value['created_at'], 'm/d/Y'), $action];
        }
        return array(
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

    public function fetchNurseListing($request)
    {
        $req = $request->all();
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        $orderby = ['name', 'role_id', 'hospice_id', 'email', 'phone', 'is_active', 'created_at'];

        $total = User::selectRaw('count(*) as total')->whereNull('deleted_at')->where('user_type', 2)->where('hospice_user_role', 3);
        $query = User::select('users.*')->where('user_type', 2)->where('hospice_user_role', 3)->whereNull('deleted_at');
        $filteredq = User::selectRaw('count(*) as total');
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where('name', 'like', '%' . $search . '%')
		->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")->orWhere('email', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where('name', 'like', '%' . $search . '%')->orWhere(DB::raw("concat(first_name, ' ', last_name)"), 'LIKE', "%".$search."%")->orWhere('email', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }
        if (Auth::user()->user_type == 2) {
            $query = $query->where('hospice_id', Auth::user()->hospice_id);
            $filteredq = $filteredq->where('hospice_id', Auth::user()->hospice_id);
            $total = $total->where('hospice_id', Auth::user()->hospice_id);
        }
        $total = $total->first();
        $totalfiltered = $total->total;
        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();
        $data = [];
        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'nurse_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'nurse_delete');
        foreach ($query as $key => $value) {
            $action = '';
            $editUrl = route('nurse-edit-user', $value->id);
            $isEdit = $isEditable ? '<a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>' : '';
            $isDelete = $isDeletable ? '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>' : '';

            $actionInner = '';
            if ($isEdit || $isDelete)
                $actionInner = '<div class="dropdown-menu dropdown-menu-right">' . $isEdit . $isDelete . '</div';
            $action = '<div class="dropdown">
              <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
              <div class="dropdown-menu dropdown-menu-right">
              '.$actionInner.'
              </div>
            </div>';
            $status = $value['is_active'] == 1 ? 'Active' : 'Inactive';
            $statusClass = $value['is_active'] == 1 ? 'success' : 'danger';
            $statusHtml = '<i class="bx bxs-circle ' . $statusClass . ' font-small-1 mr-50"></i>
            <span>' . $status . '</span>';
            if ($value['name']) {
                $name = $value['name'];
            } else {
                $name = $value['first_name'] . ' ' . $value['last_name'];
            }
            $hospiceData = NurseBranch::getAllHospiceData($value->id);
            $branchesData = NurseBranch::getAllBranchData($value->id);
            $data[] = [$name, $hospiceData ?? "-", $branchesData ?? "-", $value['email'], $value['phone'], $statusHtml, getFormatedDate($value['created_at'], 'm/d/Y'), $action];
        }
        return array(
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

    /**
     * Delete hospice
     * @param object $request
     */
    public function delete($request)
    {
        try {
            User::where('id', $request->id)->delete();
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function addAssignNurse($request)
    {
        $data = $request->all();
        try {
            $response = $this->userRepo->storeNurseData($data);
            return $response;
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function updateNurseInformation($request, $id)
    {
        try {
            $response = $this->userRepo->updateNurse($request, $id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function updateAssignNurse($request, $id)
    {
        try {
            $response = $this->userRepo->updateAssignNurse($request, $id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function addInformationAssignNurse($request)
    {
        $data = $request->all();
        try {
            $response = $this->userRepo->createHospiceAssignNurse($data);
            return $response;
        } catch (Exception $e) {
            return 'error';
        }
    }
    public function deleteUserByHospiceId($hospiceId = '')
    {
        if ($hospiceId)
        {
            $response = $this->userRepo->deleteUser($hospiceId);
            return $response;
        }
    }
}
