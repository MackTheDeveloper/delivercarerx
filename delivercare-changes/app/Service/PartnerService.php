<?php

namespace App\Service;

use App\Models\Partners;
use App\Repository\AdminRepository;
use App\Repository\PartnerRepository;
use App\Repository\ActivityRepository; 
use Hash;
use Auth;

class PartnerService
{

    protected $hospiceRepo,  $partnerRepo,$activityRepo;


    public function __construct(PartnerRepository $partnerRepo,ActivityRepository $activityRepo)
    {
        $this->partnerRepo = $partnerRepo;
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
            $response = $this->partnerRepo->create($data);
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
        return $this->partnerRepo->fetch($id);
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
        $orderby = ['name', 'address', 'city', 'state', 'zipcode','created_at','status'];

        $total = Partners::selectRaw('count(*) as total')->whereNull('deleted_at')->first();
        $query = Partners::selectRaw('partners.*')->whereNull('deleted_at');
        $filteredq = Partners::selectRaw('partners.*')->whereNull('deleted_at');
        $totalfiltered = $total->total;

        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->Where('name', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->Where('name', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->get();

        $data = [];
        $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'partner_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'partner_delete');
        foreach ($query as $key => $value) {
            $action = '';
            $editUrl = route('partners-edit', encrypt($value->id));
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

            $data[] = [$value->name, $value->address, $value->city, $value->state, $value->zipcode,getFormatedDate($value->created_at, 'm/d/Y'),$statusHtml,$action];
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

            $response = $this->partnerRepo->update($data, $id);
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
            // Delete 
            $this->partnerRepo->delete($request->id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

}
