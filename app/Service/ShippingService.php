<?php

namespace App\Service;

use App\Models\Shipping;
use App\Repository\AdminRepository;
use App\Repository\ShippingRepository;
use App\Repository\ActivityRepository;
use Hash;
use DB;
use Auth;

class ShippingService
{

    protected $shippingRepo;

    public function __construct(ShippingRepository $shippingRepo, ActivityRepository $activityRepo)
    {
        $this->shippingRepo = $shippingRepo;
        $this->activityRepo = $activityRepo;
    }

    /** 
     * Add shipping information
     * @param object $request
     */
    public function addInformation($request)
    {
        $data = $request->all();
        if ($file   =   $request->file('logo')) {
            $name  =   time() . '.' . $file->getClientOriginalExtension();
            $target_path   =   public_path() . '/assets/upload/shipping-logo';
            if ($file->move($target_path, $name)) {
                $data['logo'] = $name;
            }
        }

        try {
            $response = $this->shippingRepo->create($data);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /** 
     * Update shipping information
     * @param object $request
     */
    public function updateInformation($request, $id)
    {
        $data = $request->all();
        if ($file   =   $request->file('logo')) {
            $name  =   time() . '.' . $file->getClientOriginalExtension();
            $target_path   =   public_path() . '/assets/upload/shipping-logo';
            if ($file->move($target_path, $name)) {
                $data['logo'] = $name;
            }
        }
        try {
            $userData = array_diff_key($data, array_flip(["_token"]));
            $response = $this->shippingRepo->update($userData, $id);

            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }

    /** 
     * Fetch shipping information
     * @param $id
     */
    public function fetchInformation($id)
    {
        return $this->shippingRepo->fetch($id);
    }


    /** 
     * Add shipping information
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
        $orderby = ['logo', 'name', 'url', 'created_at', ''];

        $total = Shipping::selectRaw('count(*) as total')->whereNull('deleted_at')->first();
        $query = Shipping::select('shipping.*')->whereNull('deleted_at');
        $filteredq = Shipping::selectRaw('count(*) as total')->whereNull('deleted_at');
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where('name', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                 $query2->where('name', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();
        $data = [];
         $isEditable = whoCanCheck(config('app.arrWhoCanCheck'), 'shipping_edit');
        $isDeletable = whoCanCheck(config('app.arrWhoCanCheck'), 'shipping_delete');
        foreach ($query as $key => $value) {
            $url = '';
             $action = '';
            $editUrl = route('show-edit-shipping-form', $value->id);
            $url = '<a href="'.$value->url.'" target="_blank">'.$value->url.'</a>';

            $logoWithName = '<img class="rounded-rectangle mr-1" alt="user" height="35" width="35" src=' . $value->logo . '>'  ;

            $action = '<div class="dropdown">
              <span class="bx bx-dots-vertical-rounded font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="menu"></span>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href=' . $editUrl . '><i class="bx bx-edit-alt mr-1"></i> edit</a>
                 <a class="dropdown-item delete-record" data-id=' . $value['id'] . ' href="javascript:void(0);"><i class="bx bx-trash mr-1"></i> delete</a>
              </div>
            </div>';

            $data[] = [$logoWithName, $value->name, $url, getFormatedDate($value->created_at, 'm/d/Y'), $action];

        }
        return array(
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

    /** 
     * Delete shipping
     * @param object $request
     */
    public function delete($request)
    {
        try {
            //delete shipping carriers
            $this->shippingRepo->delete($request->id);
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }
}
