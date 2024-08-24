<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\ActivityService;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\CountryService;
use App\Service\HospiceService;
use App\Service\StateService;
use Illuminate\Http\Request;
use Auth;
use Session;
use Response;

class ActivityController extends Controller
{

    protected $activityService;

    /**
     * constructor for initialize Admin service
     *
     * @param ActivityService $activityService reference to activityService
     * 
     */
    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Listing of the hospices
     *
     * @param  Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return view('admin.reports.report-audit-trails');
    }

    /**
     * Fetch listing of activities
     *
     * @param  Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $result = $this->activityService->fetchListing($request);
        return Response::json($result);
    }
}
