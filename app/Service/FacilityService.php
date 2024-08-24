<?php

namespace App\Service;

use App\Models\Hospice;
use App\Models\Facilities;
use App\Models\Branch;
use App\Repository\AdminRepository;
use App\Repository\HospiceRepository;
use App\Repository\FacilityRepository;
use App\Repository\ActivityRepository; 
use Hash;
use Auth;

class FacilityService
{

    protected $hospiceRepo,  $facilityRepo,$activityRepo;


    public function __construct(HospiceRepository $hospiceRepo, FacilityRepository $facilityRepo,ActivityRepository $activityRepo)
    {
        $this->hospiceRepo = $hospiceRepo;
        $this->facilityRepo = $facilityRepo;
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
            $response = $this->facilityRepo->create($data);


            //save activity
            // $activityData['module_name'] = config('app.activityModules')["Facilities"];
            // $activityData['performed_by'] = Auth::user()->id;
            // $activityData['description'] = str_replace('{PARAM}',$data['name'], config('app.activityDescriptions')['Added']);
            // $this->activityRepo->create($activityData);


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
        return $this->facilityRepo->fetch($id);
    }

    public function getFacilityList()
    {
        return $this->facilityRepo->findAllFacilityList();
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
        $hospice_id = !empty($req['hospice_id']) ? decrypt($req['hospice_id']) : null;
        //dd($hospice_id);
        //$orderby = ['name', 'hospice', 'pharmacy', 'branch', 'status', 'created_at',''];
        $orderby = ['name', 'hospice.name', 'pharmacy.name', 'hospice.code', 'status', 'created_at',''];



        $total = Facilities::selectRaw('count(*) as total')
        ->join('hospice', 'hospice.id', 'facilities.hospice_id')
        ->leftJoin('pharmacy', 'pharmacy.id', 'facilities.pharmacy_id');

        $query = Facilities::selectRaw('facilities.*')
            ->join('hospice', 'hospice.id', 'facilities.hospice_id')
            ->leftJoin('pharmacy', 'pharmacy.id', 'facilities.pharmacy_id');

        $filteredq = Facilities::selectRaw('facilities.*')
            ->join('hospice', 'hospice.id', 'facilities.hospice_id')
            ->leftJoin('pharmacy', 'pharmacy.id', 'facilities.pharmacy_id');

           $query =  $query->where('facilities.status',1)->WhereNull('hospice.deleted_at')->WhereNull('pharmacy.deleted_at');
           
           $filteredq = $filteredq->where('facilities.status',1)->WhereNull('hospice.deleted_at')->WhereNull('pharmacy.deleted_at');
           
           $total = $total->where('facilities.status',1)->WhereNull('hospice.deleted_at')->WhereNull('pharmacy.deleted_at');

        if (Auth::user()->user_type == 2) {
            $query =  $query->where('facilities.hospice_id', Auth::user()->hospice_id);
            $filteredq = $filteredq->where('facilities.hospice_id', Auth::user()->hospice_id);
            $total = $total->where('facilities.hospice_id', Auth::user()->hospice_id);
        }
        

        if ($hospice_id) {
            $query =  $query->where('facilities.hospice_id', $hospice_id);
            $filteredq = $filteredq->where('facilities.hospice_id', $hospice_id);
            $total = $total->where('facilities.hospice_id', $hospice_id);
        }

        $total = $total->first();

        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->Where('facilities.name', 'like', '%' . $search . '%')
                    ->orWhere('pharmacy.name', 'like', '%' . $search . '%')
                    ->orWhere('hospice.name', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->Where('facilities.name', 'like', '%' . $search . '%')
                    ->orWhere('pharmacy.name', 'like', '%' . $search . '%')
                    ->orWhere('hospice.name', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq
            //->where('hospice_id', Auth::user()->hospice_id)
            ->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();

        $data = [];
        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'facility_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'facility_delete');
        foreach ($query as $key => $value) {
            //$branch_count = '';
            $branch_count = Branch::where('facility_id', $value->id)->where('status',1);
            if (Auth::user()->user_type == 2) {
                $branch_count =  $branch_count->where('branch.hospice_id', Auth::user()->hospice_id);
            }
            $branch_count = $branch_count->count();
            $action = '';
            $editUrl = route('admin.facilities-edit', encrypt($value->id));

            /* $action = '<div class="dropdown">
          <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>
            <a class="dropdown-item delete-record" data-id=' . $value->id . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>
          </div>
        </div>';*/
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


            $branchUrl = route('branch-list');
            if(empty($value->pharmacy->name))
            {
                $pharmacyName = 'N/A';
            }else {
                $pharmacyName = $value->pharmacy->name;
            }

            $branch_link = '<a href=' . $branchUrl . '?facility_id=' . encrypt($value->id) . '>' . $branch_count . '</a>';
            $hospicelogoWithName = '<img class="rounded-rectangle mr-1" alt="user" height="35" width="35" src=' . $value->hospice->logo . '>' . $value->hospice->name;


            $data[] = [$value->name, $hospicelogoWithName, $pharmacyName, $branch_link,  $statusHtml, getFormatedDate($value->created_at, 'm/d/Y'), $action];
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

            $response = $this->facilityRepo->update($data, $id);


            //save activity
            // $activityData['module_name'] = config('app.activityModules')["Facilities"];
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

            // $facilityData = $this->facilityRepo->fetch($request->id);
            // $activityData['module_name'] = config('app.activityModules')["Facilities"];
            // $activityData['performed_by'] = Auth::user()->id;
            // $activityData['description'] = str_replace('{PARAM}',$facilityData->name, config('app.activityDescriptions')['Deleted']);
            // $this->activityRepo->create($activityData);


            // Delete facility
            $this->facilityRepo->delete($request->id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /** 
     * Fetch facilityRepo from facilityId
     * @param $countryId
     * @return Response
     */
    public function fetchFacilityService($facilityId)
    {
        return $this->facilityRepo->fetchFacility($facilityId);
    }

    public function getDropDownList()
    {
        $data = $this->facilityRepo->dropdownData();
        return $data;
    }
}
