<?php

namespace App\Service;

use App\Http\Controllers\PlaceOrderController;
use App\Models\Branch;
use App\Models\Facility;
use App\Models\Hospice;
use App\Models\NurseBranch;
use App\Models\OfflineOrder;
use App\Models\OfflineOrderItems;
use App\Models\Pharmacy;
use App\Models\Shipping;
use App\Models\User;
use App\Repository\AdminRepository;
use App\Repository\HospiceRepository;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use App\Repository\OfflineOrderRepository;
use Hash;
use PDO;
use Str;
use Auth;
use DB;


class OfflineOrderService
{

    protected $hospiceRepo, $userRepo, $activityRepo,$offlineOrderRepo;


    /**
     * @param HospiceRepository $hospiceRepo reference to hospiceRepo
     *
     */
    public function __construct(OfflineOrderRepository $offlineOrderRepo,HospiceRepository $hospiceRepo, UserRepository $userRepo, ActivityRepository $activityRepo)
    {
        $this->hospiceRepo = $hospiceRepo;
        $this->userRepo = $userRepo;
        $this->activityRepo = $activityRepo;
        $this->offlineOrderRepo = $offlineOrderRepo;
    }

        /**
     * Add information
     * @param array $patientData
     */
    public function addInformation($input)
    {
        try {
           $return = $this->offlineOrderRepo->create($input);
            return $return;
        } catch (\Exception $e) {
            return 'error';
        }

        // Save activity for new hospice added
        /* $activityData['module_name'] = config('app.activityModules')["Hospice"];
            $activityData['performed_by'] = Auth::user()->id;
            $activityData['description'] = str_replace('{PARAM}',$data['name'], config('app.activityDescriptions')['Added']);
            $this->activityRepo->create($activityData); */
    }

    /**
     * Add fetch information
     * @param object $request
     */
    public function fetchListing($request)
    {
        //Orders->Telephonic Orders
        $req = $request->all();
        $status = $req['status'];
        $shipping_method = $req['shipping_method'];

        $startDate = $req['startDate']; //dd($startDate);
        $endDate = $req['endDate'];
        $start = $req['start'];
        $length = $req['length'];
        $search = $req['search']['value'];
        $order = $req['order'][0]['dir'];
        $column = $req['order'][0]['column'];
        //$orderby = ['date', 'rph', 'first_name', 'shipping_address', 'signature', '',''];
        $orderby = ['date', 'rph', 'first_name','shipping_method'];

        $total = OfflineOrder::selectRaw('count(*) as total')->whereNull('deleted_at')->first();
        $query = OfflineOrder::whereNull('deleted_at'); //->orderBy('id','DESC')
        $filteredq = OfflineOrder::selectRaw('count(*) as total')->whereNull('deleted_at');
        $totalfiltered = $total->total;
        if ($search != '') {
            $query->where(function ($query2) use ($search) {
                $query2->where(DB::raw('CONCAT(firstname," ",lastname)'), 'like', '%' . $search . '%')->orWhere('rph', 'like', '%' . $search . '%');
            });
            $filteredq->where(function ($query2) use ($search) {
                $query2->where(DB::raw('CONCAT(firstname," ",lastname)'), 'like', '%' . $search . '%')->orWhere('rph', 'like', '%' . $search . '%');
            });
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;
        }

        if ($req['startDate'] != "" || $req['endDate'] != "") {
            //$startDate = date('Y-m-d', strtotime($req['startDate']));
            //$endDate = date('Y-m-d', strtotime($req['endDate']));
            $startDate = date('Y-m-d', strtotime($req['startDate']));
            $endDate = date('Y-m-d', strtotime($req['endDate']));

            $query->whereBetween('date', [$startDate, $endDate]); 
            $filteredq->whereBetween('date', [$startDate, $endDate]);
            $filteredq = $filteredq->selectRaw('count(*) as total')->first();
            $totalfiltered = $filteredq->total;

        }

        if ($status != '') {
            if ($status == 'yes') {
                $query->where('signature', 'Y');
                $filteredq->where('signature', 'Y');
            } elseif ($status == 'no') {
                $query->where('signature', 'N');
                $filteredq->where('signature', 'N');
            }
        }
        if ($shipping_method != '') {
            $query->where('shipping_method', $shipping_method);
            $filteredq->where('shipping_method', $shipping_method);
        }
        $query = $query->orderBy($orderby[$column], $order)->offset($start)->limit($length)->distinct()->get();
        $data = [];
        foreach ($query as $key => $value) {
            $pdfRoute = route('generatePDF', $value->id);
            $pdfLink = '<a target="_blank" href="'.$pdfRoute.'"><i class="bx bxs-file-pdf"></i></a>';
            //$tiffRoute = route('tiffDownload', $value->id);
            //$tiffLink = '<a target="_blank" href="'.$tiffRoute.'"><i class="bx bxs-file-image"></i></a>';
            $viewItems = '<a class="item-record" data-id=' . $value->id . ' href="javascript:void(0);"> <i class="bx bx-sitemap"></i></a>';
            $dateTime = '';
            $dateTime = getFormatedDate($value->date.' '.$value->time, 'm/d/Y') ?? "-";
            $rph = $value->rph ?? "-";
            $patient_name = $value->firstname . ' ' . $value->lastname ?? "";
            $prescriber_name = $value->prescriber_name ?? "";
            $signature = $value->signature == 'Y' ? 'YES' : 'NO';
            $shippingMethodArr = config('app.shipping_methods');
            $shipping_method = $shippingMethodArr[$value->shipping_method] ?? $value->shipping_method;
            //$data[] = [$dateTime, $rph, $patient_name, $prescriber_name, $shipping_method, $signature, $pdfLink,$tiffLink,$viewItems];
            $data[] = [$dateTime, $rph, $patient_name, $prescriber_name, $shipping_method, $signature, $pdfLink,$viewItems];

        }
        return array(
            "recordsTotal" => intval($total->total),
            "recordsFiltered" => intval($totalfiltered),
            "data" => $data,
        );
    }

}
