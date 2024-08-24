<?php

namespace App\Service;

use App\Models\Hospice;
use App\Models\Facilities;
use App\Models\Branch;
use App\Repository\AdminRepository;
use App\Repository\ActivityRepository;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use Hash;
use App\Repository\StateRepository;
use Auth;

class BranchService
{

    protected   $branchRepo,$activityRepo;

    /**
     * @param BranchRepository $branchRepo reference to branchRepo
     *
     */
    public function __construct(BranchRepository $branchRepo,ActivityRepository $activityRepo)
    {
        $this->branchRepo = $branchRepo;
        $this->activityRepo = $activityRepo;
    }


    /**
     * Add email temp information
     * @param object $request
     */

    public function addInformation($request)
    {
        $data = $request->all();
        try {

            $response = $this->branchRepo->create($data);

            //save activity
            // $activityData['module_name'] = config('app.activityModules')["Branch"];
            // $activityData['performed_by'] = Auth::user()->id;
            // $activityData['description'] = str_replace('{PARAM}',$data['name'], config('app.activityDescriptions')['Added']);
            // $this->activityRepo->create($activityData);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function uploadInformation($request)
    {

        try {

            $response = $this->branchRepo->create($request);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }



    /**
     * Fetch email temp information
     * @param $id
     */
    public function fetchInformation($id)
    {
        return $this->branchRepo->fetch($id);
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
        $facility_id = !empty($req['facility_id']) ? decrypt($req['facility_id']) : null;

        //$orderby = ['name', 'code', 'hospice', 'facility', 'status', 'created_at'];
        $orderby = ['name', 'code', 'hospice.name', 'facilities.name', 'status', 'created_at'];



        $total = Branch::selectRaw('count(*) as total')
             ->leftJoin('hospice', 'hospice.id', 'branch.hospice_id')
             ->leftJoin('facilities', 'facilities.id', 'branch.facility_id');
        $query = Branch::selectRaw('branch.*')
            ->leftJoin('hospice', 'hospice.id', 'branch.hospice_id')
            ->leftJoin('facilities', 'facilities.id', 'branch.facility_id');
        $filteredq = Branch::selectRaw('branch.*')
            ->leftJoin('hospice','hospice.id',  'branch.hospice_id')
            ->leftJoin('facilities', 'facilities.id', 'branch.facility_id');

        // $query = Branch::with('hospice','facility');
        // $filteredq = Branch::with('hospice','facility');


         $query =  $query->where('branch.status',1)->WhereNull('branch.deleted_at')->WhereNull('hospice.deleted_at')->WhereNull('facilities.deleted_at');
           $filteredq = $filteredq->where('branch.status',1)->WhereNull('branch.deleted_at')->WhereNull('hospice.deleted_at')->WhereNull('facilities.deleted_at');
           $total = $total->where('branch.status',1)->WhereNull('hospice.deleted_at')->WhereNull('facilities.deleted_at');
          
          

        if (Auth::user()->user_type == 2) {
            $query =  $query->where('branch.hospice_id', Auth::user()->hospice_id);
            $filteredq = $filteredq->where('branch.hospice_id', Auth::user()->hospice_id);
            $total = $total->where('branch.hospice_id', Auth::user()->hospice_id);
        }
        

        if ($facility_id) {
            $query =  $query->where('facility_id', $facility_id);
            $filteredq = $filteredq->where('facility_id', $facility_id);
            $total = $total->where('facility_id', $facility_id);
        }

        $total = $total->first();
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->Where('branch.name', 'like', '%' . $search . '%')
                    ->orWhere('branch.code', 'like', '%' . $search . '%')
                    ->orWhere('hospice.name', 'like', '%' . $search . '%')
                    ->orWhere('facilities.name', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->Where('branch.name', 'like', '%' . $search . '%')
                    ->orWhere('branch.code', 'like', '%' . $search . '%')
                    ->orWhere('hospice.name', 'like', '%' . $search . '%')
                   ->orWhere('facilities.name', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();

        $data = [];
        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'branch_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'branch_delete');
        foreach ($query as $key => $value) {
            
            $action = '';
            $editUrl = route('admin.branch-edit', encrypt($value->id));

            $isEdit = $isEditable ? '<a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>' : '';
            $isDelete = $isDeletable ? '<a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>' : '';

            $actionInner = '';
            if ($isEdit || $isDelete)
                $actionInner = '<div class="dropdown-menu dropdown-menu-right">' . $isEdit . $isDelete . '</div';

            $action = '<div class="dropdown">
          <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>' . $actionInner . '</div>';
            $status = $value->status == 1 ? 'Active' : 'Inactive';
            $statusClass = $value->status == 1 ? 'success' : 'danger';
            $statusHtml = '<i class="bx bxs-circle ' . $statusClass . ' font-small-1 mr-50"></i>
             <span>' . $status . '</span>';
            $hospicename = ($value->hospice)?$value->hospice->name:'N/A';
            $hospicelogo = ($value->hospice)?$value->hospice->logo:'N/A';
            $hospicelogoWithName = '<img class="rounded-rectangle mr-1" alt="user" height="35" width="35" src=' . $hospicelogo . '>' . $hospicename;

            $data[] = [$value->name, ($value->code)?$value->code:'N/A', $hospicelogoWithName, ($value->facility)?$value->facility->name:'N/A',  $statusHtml, getFormatedDate($value->created_at, 'm/d/Y'), $action];
        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }


    public function updateInformation($request, $id)
    {
        $data = $request->except(['_token', '_method']);

        try {

            $response = $this->branchRepo->update($data, $id);

            //save activity
            // $activityData['module_name'] = config('app.activityModules')["Branch"];
            // $activityData['performed_by'] = Auth::user()->id;
            // $activityData['description'] = str_replace('{PARAM}',$data['name'], config('app.activityDescriptions')['Updated']);
            // $this->activityRepo->create($activityData);

            if ($response == 1) {
                return 'success';
            }
        } catch (Exception $e) {
            return 'error';
        }
    }

    /**
     * Delete hospice
     * @param object $request
     */
    public function delete($request)
    {
        try {

            // $branchData = $this->branchRepo->fetch($request->id);
            // $activityData['module_name'] = config('app.activityModules')["Branch"];
            // $activityData['performed_by'] = Auth::user()->id;
            // $activityData['description'] = str_replace('{PARAM}',$branchData->name, config('app.activityDescriptions')['Deleted']);
            // $this->activityRepo->create($activityData);


            // Delete branch
            $this->branchRepo->delete($request->id);


            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }


    /**
     * Fetch cities from stateId
     * @param $stateId
     * @return Response
     */
    public function fetchBranchesService($branchId)
    {
        return $this->branchRepo->fetchBranches($branchId);
    }
    public function getDropDownList()
    {
        $data = $this->branchRepo->dropdownData();
        return $data;
    }

    public function getDropDownListBranchAndHospice($hospiceId = '')
    {
        $data = $this->branchRepo->dropdownDataBranchAndHospice($hospiceId);
        return $data;
    }

    public function getDropDownListNurses()
    {
        $data = $this->branchRepo->getDropDownListNurses();
        return $data;
    }

    /**
     * Fetch branch Data from branch code
     * @param $branchCode
     * @return Response
     */
    public function fetchBranchDataFromcode($branchCode)
    {
        return $this->branchRepo->fetchBranchDataFromcode($branchCode);
    }

    /**
     * Fetch branches from hospiceId
     * @param $hospiceId
     * @return Response
     */
    public function fetchBranchesOfHospice($hospiceId)
    {
        $data = $this->branchRepo->fetchBranchesOfHospice($hospiceId);
        return $data;
    }

}
