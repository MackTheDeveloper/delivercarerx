<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RefillOrder;
use App\Service\ActivityService;
use App\Service\AdminService;
use App\Service\BranchService;
use App\Service\RefillsInQueueService;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;

class RefillsInQueueController extends Controller
{

    protected $refillsInQueueService,$activityServie,$branchService;

    /**
     * constructor for initialize Admin service
     *
     * @param RefillsInQueueService $refillsInQueueService reference to refillsInQueueService
     *
     */
    public function __construct(RefillsInQueueService $refillsInQueueService, ActivityService $activityServie, BranchService $branchService)
    {
        $this->refillsInQueueService = $refillsInQueueService;
        $this->activityServie = $activityServie;
        $this->branchService = $branchService;
    }

    /**
     * Listing of the refills
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $branch = $this->branchService->getDropDownListBranchAndHospice();
        return view('admin.refillsInQueue.refills-listing', compact('branch'));
    }

    public function list(Request $request)
    {
        $result = $this->refillsInQueueService->fetchListing($request);
        return Response::json($result);
    }

}
