<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\User;
use App\Repository\CustomerRepository;
use App\Repository\HospiceRepository;
use App\Repository\UserRepository;
use Hash;
use Str;

class customerService
{

    protected $hospiceRepo, $userRepo, $customerRepo;

    /**
     * @param HospiceRepository $hospiceRepo reference to hospiceRepo
     * 
     */
    public function __construct(HospiceRepository $hospiceRepo, UserRepository $userRepo, CustomerRepository $customerRepo)
    {
        $this->hospiceRepo = $hospiceRepo;
        $this->userRepo = $userRepo;
        $this->customerRepo = $customerRepo;
    }


    /** 
     * Fetch customer information
     * @param $id
     */
    public function fetchList()
    {
        return $this->customerRepo->getDropDownList();
    }

    /** 
     * Add customer information
     * @param object $request
     */
    public function addInformation($request)
    {
        try {
            // Save user information
            $this->customerRepo->createcustomerUser($request);
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
        try {
            $userData = array_diff_key($data, array_flip(["_token"]));
            $response = $this->customerRepo->update($userData, $id);
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
        return $this->customerRepo->fetch($id);
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
         $orderby = ['name', 'address_1', 'states.name', 'cities.name', '', '', 'is_active', 'created_at'];



        $total = customer::selectRaw('count(*) as total')->first();
        $data =  customer::whereNull('deleted_at')->get();

        $query = customer::selectRaw('customer.*,states.name as stateName,cities.name as cityName')
            ->join('states', 'states.id', 'customer.state_id')
            ->join('cities', 'cities.id', 'customer.city_id');

        $filteredq = customer::join('states', 'states.id', 'customer.state_id')->join('cities', 'cities.id', 'customer.city_id');

        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(address_1,' ',address_2)"), 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('cities.name', 'like', '%' . $search . '%')
                    ->orWhere('states.name', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where(DB::raw("CONCAT(address_1,' ',address_2)"), 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('cities.name', 'like', '%' . $search . '%')
                    ->orWhere('states.name', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        $query = $query->get();

        $data = [];
        foreach ($query as $key => $value) {

            $action = '';
            $editUrl = route('show-edit-customer-form', $value->id);

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
            $data[] = [$value->name, $value->address_1 . '' . $value->address_2, $value->cityName, $value->stateName, $statusHtml, getFormatedDate($value->created_at, 'd/m/Y'), $action];
        }
        return array(
            "draw" => intval($_REQUEST['draw']),
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
      
    }

    /** 
     * Delete customer
     * @param object $request
     */
    public function delete($request)
    {
        try {
            customer::where('id', $_REQUEST['id'])->delete();
            return 'success';
        } catch (Exception $e) {
            return 'error';
        }
    }
}

