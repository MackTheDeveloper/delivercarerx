<?php

namespace App\Service;

use App\Models\Activities;
use App\Models\Hospice;
use App\Models\City;
use App\Models\Facilities;
use App\Models\User;
use App\Repository\ActivityRepository;
use App\Repository\AdminRepository;
use App\Repository\CommonRepository;
use App\Repository\HospiceRepository;
use App\Repository\FacilityRepository;
use App\Repository\PharmacyRepository;
use App\Repository\UserRepository;
use App\Repository\BranchRepository;
use Hash;
use DB;
use Auth;

class ActivityService
{

    protected $activityRepo, $userRepo, $commonRepo,$branchReo,$hospiceRepo,$facilityRepo,$pharmacyRepo;

    /**
     * @param ActivityRepository $activityRepo reference to activityRepo
     * @param UserRepository $userRepo reference to userRepo
     *
     */
    public function __construct(ActivityRepository $activityRepo, UserRepository $userRepo, User $commonRepo,BranchRepository $branchReo,HospiceRepository  $hospiceRepo,FacilityRepository $facilityRepo,PharmacyRepository $pharmacyRepo)
    {
        $this->activityRepo = $activityRepo;
        $this->userRepo = $userRepo;
        $this->branchReo = $branchReo;
        $this->hospiceRepo = $hospiceRepo;
        $this->facilityRepo = $facilityRepo;
        $this->pharmacyRepo = $pharmacyRepo;
        $this->common = new CommonRepository($commonRepo);
    }

    /**
     * Add activity information
     * @param array $data
     */
    public function addInformation($data)
    {
        try {
            $this->activityRepo->create($data);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /**
     * fetch activity information
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
        $orderby = ['activities.module_name', 'users.name', 'activities.created_at', 'activities.description'];


        $total = Activities::selectRaw('count(*) as total');

        $query = Activities::selectRaw('activities.*,users.name as userName')
            ->join('users', 'users.id', 'activities.performed_by');
        $filteredq = Activities::join('users', 'users.id', 'activities.performed_by');

        if (Auth::user()->user_type == 2) {
            $hospiceUsers = $this->userRepo->getUserIdsOfHospice();
            $total->whereIn('activities.performed_by', $hospiceUsers);
            $query->whereIn('activities.performed_by', $hospiceUsers);
            $filteredq->whereIn('activities.performed_by', $hospiceUsers);
        }

        if (!empty($request->action)) {
            $total = $total->where('module_name', $request->action);
            $filteredq = $filteredq->where('module_name', $request->action);
            $query = $query->where('module_name', $request->action);
        }

        if (!empty($request->startDate) && !empty($request->endDate)) {
            /* $startDate = date($request->startDate);
            $endDate = date($request->endDate); */
            $startDate = $request->startDate;
            $endDate = $request->endDate;
            $filteredq = $filteredq->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw("date_format(activities.created_at,'%Y-%m-%d')"), [$startDate, $endDate]);
            });
            $query = $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw("date_format(activities.created_at,'%Y-%m-%d')"), [$startDate, $endDate]);
            });
            $total = $total->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw("date_format(activities.created_at,'%Y-%m-%d')"), [$startDate, $endDate]);
            });
        }

        $total = $total->first();

        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(users.first_name,' ',users.last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('users.name', 'like', '%' . $search . '%')
                    ->orWhere('activities.module_name', 'like', '%' . $search . '%')
                    ->orWhere('activities.description', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(users.first_name,' ',users.last_name)"), 'like', '%' . $search . '%')
                    ->orWhere('users.name', 'like', '%' . $search . '%')
                    ->orWhere('activities.module_name', 'like', '%' . $search . '%')
                    ->orWhere('activities.description', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }


        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();

        $data = [];

        foreach ($query as $key => $value) {
            $data[] = [$value->module_name, $value->userName, getFormatedDate($value->created_at, 'd/m/Y h:i:s'), $value->description];
        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }


    /**
     * logs
     */
    public function logs($event, $module, $request, $moduleForAddOperation = "General", $keyForAddOperation, $valueForAddOperation)
    {
        $moduleForAddOperation = empty($moduleForAddOperation) ? "General" : $moduleForAddOperation;
        if ($event == 'added') {
            $activityData['description'] = str_replace($keyForAddOperation, $valueForAddOperation, config('app.activityDescriptions')[$moduleForAddOperation]['Added']);
            $this->activityRepo->create($activityData);
        } else if ($event == 'deleted') {
            $activityData['module_name'] = $module;
            $activityData['description'] = str_replace($keyForAddOperation, $valueForAddOperation, config('app.activityDescriptions')[$moduleForAddOperation]['Deleted']);
            $this->activityRepo->create($activityData);
        } else if ($event == 'import') {
            $activityData['module_name'] = $module;
            $activityData['description'] = str_replace($keyForAddOperation, $valueForAddOperation, config('app.activityDescriptions')[$moduleForAddOperation]['Imported']);
            $this->activityRepo->create($activityData);
        }
        else if ($event == 'export') {
            $activityData['module_name'] = $module;
            $activityData['description'] = str_replace($keyForAddOperation, $valueForAddOperation, config('app.activityDescriptions')[$moduleForAddOperation]['Exported']);
            $this->activityRepo->create($activityData);
        }
        else {
            /* $activityData['module_name'] = $module;
            $activityData['description'] = str_replace($keyForAddOperation, $valueForAddOperation, config('app.activityDescriptions')[$moduleForAddOperation]['Updated']);
            $this->activityRepo->create($activityData); */
            //dd($request);
            $changes = $request->isDirty() ? $request->getDirty() : false;

            if ($changes) {
                $attrName = '';
                foreach ($changes as $attr => $value) {
                    $attrName = ucfirst($attr);
                    if (isset(config('app.renamedAttributes')[$attr]))
                        $attrName = config('app.renamedAttributes')[$attr];

                    $oldValue = $request->getOriginal($attr);
                    $newValue = $request->$attr;
                    if (isset(config('app.attrStoredWithId')[$attr])) {
                        $oldValue = ($oldValue) ? $this->common->findAttrByPk(config('app.attrStoredWithId')[$attr], $oldValue) : null;
                        $newValue = $this->common->findAttrByPk(config('app.attrStoredWithId')[$attr], $newValue);
                    }
                    if ($attr == 'is_active') {
                        $oldValue = ($oldValue == 1) ? 'Active' : 'Inactive';
                        $newValue = ($newValue == 1) ? 'Active' : 'Inactive';
                    }
                    if ($attr == 'role_id') {
                       $oldValue = (($oldValue == 1) ? 'Site Admin' : ($oldValue == 2)) ? 'Pharmacy Admin' : 'User';
                        $newValue = (($newValue == 1) ? 'Site Admin' : ($newValue == 2)) ? 'Pharmacy Admin' : 'User';
                    }
                    if ($attr == 'gender') {
                        $oldValue = ($oldValue == 1) ? 'Male' : 'Female';
                        $newValue = ($newValue == 1) ? 'Male' : 'Female';
                    }
                    if ($attr == 'hospice_user_role') {
                        $oldValue = ($oldValue == 1) ? 'Admin' : 'Branch Admin';
                        $newValue = ($newValue == 1) ? 'Admin' : 'Branch Admin';
                    }
                    if ($attr == 'pharmacy_id' && !(implode(',', $newValue) == $oldValue)) {
                        if ($oldValue) {
                            $oldValue = $this->pharmacyRepo->getBranchName(explode(',', $oldValue));
                        } else {
                            $oldValue = 'Blank';
                        }
                        if ($newValue) {
                            $newValue = $this->pharmacyRepo->getBranchName($newValue);
                        } else {
                            $newValue = 'Blank';
                        }
                    } else if ($attr == 'pharmacy_id' && (implode(',', $newValue) == $oldValue)) {
                        continue;
                    }
                    if ($attr == 'facility_id') {
                        if ($oldValue) {
                            $oldValue = $this->facilityRepo->getBranchName($oldValue);
                        } else {
                            $oldValue = 'Blank';
                        }
                        if ($newValue) {
                            $newValue = $this->facilityRepo->getBranchName($newValue);
                        } else {
                            $newValue = 'Blank';
                        }
                    }
                        if ($attr == 'hospice_id') {

                            $oldValue = ($oldValue) ? $this->hospiceRepo->getBranchName($oldValue) : null;
                            $newValue = $this->hospiceRepo->getBranchName($newValue);
                        }
                        if ($attr == 'facility_id') {
                            $oldValue = ($oldValue) ? $this->facilityRepo->getBranchName($oldValue) : null;
                            $newValue = $this->facilityRepo->getBranchName($newValue);
                        }
                        if ($attr == 'branch_id' && !(implode(',', $newValue) == $oldValue)) {
                            if ($oldValue) {
                                $oldValue = $this->branchReo->getBranchName(explode(',', $oldValue));
                            } else {
                                $oldValue = 'Blank';
                            }
                            if ($newValue) {
                                $newValue = $this->branchReo->getBranchName($newValue);
                            } else {
                                $oldValue = 'Blank';
                            }
                        } else if ($attr == 'branch_id' && (implode(',', $newValue) == $oldValue)) {
                            continue;
                        }
                        $activityData['module_name'] = $module;
                        $activityData['description'] = "Updated $attrName From <strong>{$oldValue}</strong> To <strong>{$newValue}</strong>";
                        $this->activityRepo->create($activityData);
                    }
                }
            }
        }

}
