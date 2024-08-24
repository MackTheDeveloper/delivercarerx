<?php

namespace App\Repository;

use App\Models\Branch;
use App\Models\Facilities;
use App\Models\PasswordReset;
use App\Models\NurseBranch;
use App\Models\User;
use Mail;
use Auth;
use App\Models\Hospice;
use App\Repository\UserRepository;

class BranchRepository
{

    /**
     * Fetch the Branch
     * @return Branch list
     */
    public function fetchBranches($facility_id)
    {
        $branches = Branch::select('name', 'id')->where('facility_id', $facility_id)->where('status',1)->get();
        return $branches;
    }

    public function dropdownData()
    {
        return Branch::select('name', 'id')->whereNull('deleted_at')->get();
    }

   public function dropdownDataBranchAndHospice($hospiceId = '')
    {
        $data = Hospice::select('hospice.code as hcode','hospice.name as hname','branch.id as bid','branch.name as bname','branch.code as bcode')
                ->join('facilities','facilities.hospice_id','hospice.id')
                ->join('branch','branch.facility_id','facilities.id')
                ->where('hospice.is_active',1)
                ->where('branch.status',1);
        if ($hospiceId)
        {
            //$data->where('hospice.id', $hospiceId);
        }
        // for hospice admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 1)
        {
            $hospice_id = Auth::user()->hospice_id;
            $data->where('hospice.id', $hospice_id);
        }
        // for pharmacy admin & delivercare user
        if((Auth::user()->user_type == 1 && Auth::user()->role_id == 2) || (Auth::user()->user_type == 1 && Auth::user()->role_id == 3))
        {
            $pharmacy_id = explode(",",Auth::user()->pharmacy_id);
            $data->whereIn('facilities.pharmacy_id',$pharmacy_id);
        }
        // for branch admin
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 2)
        {
            $branch_id = explode(",",Auth::user()->branch_id);

            if(!empty(Auth::user()->branch_id))
            {
                $data->whereIn('branch.id',$branch_id);
            }
            else
            {
                $data->where('branch.id',0);
            }
            
        }
        // for nurse
        if(Auth::user()->user_type == 2 && Auth::user()->hospice_user_role == 3)
        {
            $data->join('nurse_branches', 'nurse_branches.branch_id', 'branch.id')
            ->where('nurse_branches.user_id', Auth::user()->id);
        }
        $data = $data->get();
        $branchAndHospice = [];
        foreach ($data as $key => $value) {
             $branchAndHospice[$value->bid]['id'] = $value->bid;
             $branchAndHospice[$value->bid]['value'] = $value->hname . ' (' . $value->hcode . ')' . ' - ' . $value->bname . ' (' . $value->bcode . ') ';
        }
            return $branchAndHospice;
    }
    
    public function getBranchAndHospiceById($branchId = '')
    {
        $data = Branch::with('hospice')->where('id', $branchId)->first();
        if ($data) {
            $branchAndHospice = $data['name'] . ' (' . $data['code'] . ')' . ' - ' . $data->hospice->name . ' (' . $data->hospice->code . ') ';
        }
        return $branchAndHospice ?? '';
    }

    /**
     * Fetch the Branch of the hospice
     * @param $hospiceId
     * @return Branch list
     */
    public function fetchBranchesOfHospice($hospiceId)
    {
        $branches = Branch::where('hospice_id', $hospiceId)->where('status',1)->pluck('id')->toArray();
        return $branches;
    }

    public function getDropDownListNurses()
    {
        $nurses = [];
        $value = User::select('id', 'email', 'name')
            ->where('user_type', 2)
            ->where('status',1)
            ->where('hospice_user_role', 3)
            ->whereNull('deleted_at')->get();

        foreach ($value as $key => $value) {
            $nurses[$key]['id'] = $value['id'];
            $nurses[$key]['first_name'] = $value['first_name'];
        }
        return $nurses;
    }

    /**
     * Send email
     * @return bool
     */
    public function create($data)
    {
        return Branch::create($data);
    }

    public function fetch($id)
    {
        return Branch::where('id', $id)->first();
    }

    public function update($data, $id = null)
    {
        return Branch::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Branch::where('id', $id)->delete();
    }

    public function getBranchName($id)
    {
        $returnVal = '[ ';
        foreach ($id as $key => $value) {
            $model = Branch::select('name')->where('id', $value)->first();
            if ($model){
                $returnVal .= ' ' . $model->name . ' ';
            }
        }
        $returnVal .= ' ]';
        return $returnVal;
    }

    /**
     * Fetch branch Data from the code
     * @param array $branchCode
     * @return Response
     */
    public function fetchBranchDataFromcode($branchCode)
    {
        $branch = Branch::where('code', $branchCode)->first();
        return $branch;
    }
}
