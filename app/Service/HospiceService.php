<?php

namespace App\Service;

use App\Models\Hospice;
use App\Models\Facilities;
use App\Repository\ActivityRepository;
use App\Repository\AdminRepository;
use App\Repository\HospiceRepository;
use App\Repository\UserRepository;
use Hash;
use DB;
use Auth;

class HospiceService
{

    protected $hospiceRepo, $userRepo, $activityRepo;

    /**
     * @param HospiceRepository $hospiceRepo reference to hospiceRepo
     * @param UserRepository $userRepo reference to userRepo
     * @param ActivityRepository $activityRepo reference to activityRepo
     *
     */
    public function __construct(HospiceRepository $hospiceRepo, UserRepository $userRepo, ActivityRepository $activityRepo)
    {
        $this->hospiceRepo = $hospiceRepo;
        $this->userRepo = $userRepo;
        $this->activityRepo = $activityRepo;
    }

    /**
     * Add hospice information
     * @param object $request
     */
    public function addInformation($request)
    {
        $data = $request->all();

        if ($file   =   $request->file('logo')) {
            $name  =   time() . '.' . $file->getClientOriginalExtension();
            $target_path   =   public_path() . '/assets/upload/hospice-logo';
            if ($file->move($target_path, $name)) {
                $data['logo'] = $name;
            }
        }
        try {
            $response = $this->hospiceRepo->create($data);

            // Save user information
            $userData['user_type'] = 2;
            $userData['hospice_user_role'] = 1;
            $userData['name'] = $data['hospice_user_name'];
            $userData['email'] = $data['hospice_user_email'];
            $userData['password'] = Hash::make($data['hospice_user_password']);
            $userData['hospice_id'] = $response->id;
            $userData['is_active'] = 1;
            $this->userRepo->createHospiceUser($userData);

            // Save activity for new hospice added
            /* $activityData['module_name'] = config('app.activityModules')["Hospice"];
            $activityData['performed_by'] = Auth::user()->id;
            $activityData['description'] = str_replace('{PARAM}',$data['name'], config('app.activityDescriptions')['Added']);
            $this->activityRepo->create($activityData); */

            return 'success';
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
        $data = $request->all();
        if ($file   =   $request->file('logo')) {
            $name  =   time() . '.' . $file->getClientOriginalExtension();
            $target_path   =   public_path() . '/assets/upload/hospice-logo';
            if ($file->move($target_path, $name)) {
                $data['logo'] = $name;
            }
        }
        try {
            $userData = array_diff_key($data, array_flip(["_token"]));
            $response = $this->hospiceRepo->update($userData, $id);

            // Save activity for hospice updated
            /* $activityData['module_name'] = config('app.activityModules')["Hospice"];
            $activityData['performed_by'] = Auth::user()->id;
            $activityData['description'] = str_replace('{PARAM}',$data['name'], config('app.activityDescriptions')['Updated']);
            $this->activityRepo->create($activityData); */

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
        return $this->hospiceRepo->fetch($id);
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
        $orderby = ['hospice.name', 'code', 'address_1', 'states.name', 'cities.name', '', '', 'is_active', 'created_at'];


        $total = Hospice::selectRaw('count(*) as total')->first();
        $query = Hospice::selectRaw('hospice.*,states.name as stateName,cities.name as cityName')
            ->leftJoin('states', 'states.id', 'hospice.state_id')
            ->leftJoin('cities', 'cities.id', 'hospice.city_id');
        $filteredq = Hospice::leftJoin('states', 'states.id', 'hospice.state_id')->join('cities', 'cities.id', 'hospice.city_id');
        /* $query = Hospice::select('hospice.*')
            //selectRaw('hospice.*,state.name as stateName,city.name as cityName')
            ->with(['statee' => function ($query) {
                $query->select('states.name as stateName');
            }])
            ->with(['cityy' => function ($query) {
                $query->select('cities.name as cityName');
            }]); */

        //->with(['state', 'city']);
        /* $filteredq = Hospice::with(['state', 'city']); */
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(address_1,' ',address_2)"), 'like', '%' . $search . '%')
                    ->orWhere('hospice.name', 'like', '%' . $search . '%')
                    ->orWhere('hospice.code', 'like', '%' . $search . '%')
                    ->orWhere('cities.name', 'like', '%' . $search . '%')
                    ->orWhere('states.name', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(address_1,' ',address_2)"), 'like', '%' . $search . '%')
                    ->orWhere('hospice.name', 'like', '%' . $search . '%')
                    ->orWhere('hospice.code', 'like', '%' . $search . '%')
                    ->orWhere('cities.name', 'like', '%' . $search . '%')
                    ->orWhere('states.name', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();

        $data = [];

        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'hospice_delete');
        foreach ($query as $key => $value) {

            $action = '';
            $editUrl = route('show-edit-hospice-form', $value->id);


            $logoWithName = '<img class="rounded-rectangle mr-1" alt="user" height="35" width="35" src=' . $value->logo . '>' . $value->name;
            $facility = "<a href='{{route('show-edit-hospice')}}'>3</a>";
            //$facility = count($this->hospiceRepo->getHospiceFacilities($value->id));

            $users = "<ul class='list-unstyled users-list m-0  d-flex align-items-center'>";
            $hospiceUsers = $this->userRepo->fetchHospiceUsers($value->id);
            $i = 1;
            foreach ($hospiceUsers as $userKey => $userValue) {
                if ($i > 3)
                    continue;
                if (!empty($userValue->profile_picture))
                    $profilePic = asset('assets/upload/profile-pic/' . $userValue->profile_picture);
                else
                    $profilePic = asset('assets/img/user-default.jpg');
                $users .= "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='bottom' title=" . $userValue['name'] . " class='avatar pull-up'>
                <img src=" . $profilePic . " alt='Avatar' height='30' width='30'>
              </li>";
                $i++;
            }
            $users .= "</ul>";
            if (count($hospiceUsers) > 3)
                $users .= "<span class='font-small-1'>+" . (count($hospiceUsers) - 3) . " users</span>";

            $isEdit = $isEditable ? '<a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>' : '';
            $isDelete = $isDeletable ? '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>' : '';

            $actionInner = '';
            if ($isEdit || $isDelete)
                $actionInner = '<div class="dropdown-menu dropdown-menu-right">' . $isEdit . $isDelete . '</div';

            $action = '<div class="dropdown">
          <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>' . $actionInner . '</div>';
            $status = $value->is_active == 1 ? 'Active' : 'Inactive';
            $statusClass = $value->is_active == 1 ? 'success' : 'danger';
            $statusHtml = '<i class="bx bxs-circle ' . $statusClass . ' font-small-1 mr-50"></i>
        <span>' . $status . '</span>';

            $facility_count = Facilities::where('hospice_id', $value->id)->count();
            $facilityUrl = route('facilities-list');
            $facility_link = '<a href=' . $facilityUrl . '?hospice_id=' . encrypt($value->id) . '>' . $facility_count . '</a>';
            if(empty($value->address_1))
            {
                $address1 = '-';
            }else{
                $address1 = $value->address_1;
            }

            if(empty($value->cityName))
            {
                $cityName = '-';
            }else{
                $cityName = $value->cityName;
            }

            if(empty($value->stateName))
            {
                $stateName = '-';
            }else{
                $stateName = $value->stateName;
            }


            $data[] = [$logoWithName, $value->code, $address1 . ' ' . $value->address_2, $cityName,$stateName, $facility_link, $users,  $statusHtml, getFormatedDate($value->created_at, 'm/d/Y'), $action];
        }
        return array(
            "draw" => intval($_REQUEST['draw']),
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
            // Save activity for hospice deleted
            /* $hospiceData = $this->hospiceRepo->fetch($request->id);
            $activityData['module_name'] = config('app.activityModules')["Hospice"];
            $activityData['performed_by'] = Auth::user()->id;
            $activityData['description'] = str_replace('{PARAM}',$hospiceData->name, config('app.activityDescriptions')['Deleted']);
            $this->activityRepo->create($activityData); */

            // Delete hospice
            $this->hospiceRepo->delete($request->id);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function uploadInformation($request)
    {

        try {

            $response = $this->hospiceRepo->createUpdate($request);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function getHospiceList()
    {
        return $this->hospiceRepo->findAllHospiceList();
    }

    public function getDropDownList()
    {
        $data = $this->hospiceRepo->getListNameAndCode();
        return $data;
    }

    public function getHospiceFacilitiesList($id)
    {
        return $this->hospiceRepo->getHospiceFacilities($id);
    }

    public function hospiceByIdZeroService()
    {
        return $this->hospiceRepo->getHospiceByIdZeroRepo();
    }
}
