<?php

namespace App\Service;

use App\Models\Pharmacy;
use App\Models\User;
use App\Repository\PharmacyRepository;
use App\Repository\HospiceRepository;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use Hash;
use Str;
use DB;
use Auth;

class PharmacyService
{

    protected $hospiceRepo, $userRepo, $pharmacyRepo;

    /**
     * @param HospiceRepository $hospiceRepo reference to hospiceRepo
     *
     */
    public function __construct(HospiceRepository $hospiceRepo, UserRepository $userRepo, PharmacyRepository $pharmacyRepo, ActivityRepository $activityRepo)
    {
        $this->hospiceRepo = $hospiceRepo;
        $this->userRepo = $userRepo;
        $this->pharmacyRepo = $pharmacyRepo;
        $this->activityRepo = $activityRepo;
    }


    /**
     * Fetch pharmacy information
     * @param $id
     */
    public function fetchList()
    {
        return $this->pharmacyRepo->getDropDownList();
    }

    /**
     * Add pharmacy information
     * @param object $request
     */
    public function addInformation($request)
    {
        try {
            // Save user information
            $data = $this->pharmacyRepo->createPharmacyUser($request);
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

        // if ($file   =   $request->file('logo')) {
        //     $name  =   time() . '.' . $file->getClientOriginalExtension();
        //     $target_path   =   public_path() . '/assets/upload/hospice-logo';
        //     if ($file->move($target_path, $name)) {
        //         $data['logo'] = $name;
        //     }
        // }
        try {
            $userData = array_diff_key($data, array_flip(["_token"]));
            $response = $this->pharmacyRepo->update($userData, $id);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /**
     * Fetch pharmacy information
     * @param $id
     */
    public function fetchInformation($id)
    {
        return $this->pharmacyRepo->fetch($id);
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
        $orderby = ['name', 'address_1', 'states.name', 'cities.name', 'location', '', '', 'is_active', 'created_at'];


        $total = Pharmacy::selectRaw('count(*) as total')->first();
        $query = Pharmacy::selectRaw('pharmacy.*,states.name as stateName,cities.name as cityName')
            ->join('states', 'states.id', 'pharmacy.state_id')
            ->join('cities', 'cities.id', 'pharmacy.city_id');
        $filteredq = Pharmacy::join('states', 'states.id', 'pharmacy.state_id')->join('cities', 'cities.id', 'pharmacy.city_id');
        /* $query = Hospice::select('hospice.*')
            //selectRaw('pharmacy.*,state.name as stateName,city.name as cityName')
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
                    ->orWhere('pharmacy.name', 'like', '%' . $search . '%')
                    ->orWhere('cities.name', 'like', '%' . $search . '%')
                    ->orWhere('states.name', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(address_1,' ',address_2)"), 'like', '%' . $search . '%')
                    ->orWhere('pharmacy.name', 'like', '%' . $search . '%')
                    ->orWhere('cities.name', 'like', '%' . $search . '%')
                    ->orWhere('states.name', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->get();

        $data = [];
        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'pharmacy_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'pharmacy_delete');

        foreach ($query as $key => $value) {

            $action = '';
            $editUrl = route('show-edit-pharmacy-form', $value->id);

            // $logoWithName = '<img class="rounded-rectangle mr-1" alt="user" height="35" width="35" src=' . $value->logo . '>' . $value->name;
            // $facility = "<a href='{{route('show-edit-pharmacy')}}'>3</a>";
            //$facility = count($this->hospiceRepo->getHospiceFacilities($value->id));
            $users = "<ul class='list-unstyled users-list m-0  d-flex align-items-center'>";

            $pharmacyUsers = $this->userRepo->fetchPharmacyUsers($value->id);
            // dd($pharmacyUsers);
            if(!empty($pharmacyUsers))
            $i = 1;
            foreach ($pharmacyUsers as $userKey => $userValue) {
                if ($i > 3)
                    continue;
                if (!empty($userValue['profile_picture']))
                    $profilePic = asset('assets/upload/profile-pic/' . $userValue['profile_picture']);
                else
                    $profilePic = asset('assets/img/user-default.jpg');
                $users .= "<li data-toggle='tooltip' data-popup='tooltip-custom' data-placement='bottom' title=" . $userValue['name'] . " class='avatar pull-up'>
                <img src=" . $profilePic . " alt='Avatar' height='30' width='30'>
              </li>";
                $i++;
            }
            $users .= "</ul>";
            if (count($pharmacyUsers) > 3)
                $users .= "<span class='font-small-1'>+" . (count($pharmacyUsers) - 3) . " users</span>";
            elseif (empty($pharmacyUsers))
                $users = "<span class='font-small-1'>" . 0 . " users</span>";

            if (!empty($value->google_link))
                $location = "<a href=" . $value->google_link . " target='_blank'><i class='bx bxs-map success font-medium-1 mr-50'></i></a>";
            else
                $location = "-";

            $action = '<div class="dropdown">
          <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>
              <a class="dropdown-item delete-record" data-id=' . $value['id'] . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>
          </div>
        </div>';
            $status = $value->is_active == 1 ? 'Active' : 'Inactive';
            $statusClass = $value->is_active == 1 ? 'success' : 'danger';
            $statusHtml = '<i class="bx bxs-circle ' . $statusClass . ' font-small-1 mr-50"></i>
        <span>' . $status . '</span>';
            $data[] = [$value->name, $value->address_1 . ' ' . $value->address_2, $value->cityName, $value->stateName, $users, $location, $statusHtml, getFormatedDate($value->created_at, 'm/d/Y'), $action];
        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

    /**
     * Delete pharmacy
     * @param object $request
     */
    public function delete($request)
    {
        try {
            $this->pharmacyRepo->delete($request->id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }
}
