<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Service\AdminService;
use App\Service\AdminServie;
use App\Service\CityService;
use App\Service\HospiceService;
use App\Service\StateService;
use Illuminate\Http\Request;
use Auth;
use Session;

class CommonController extends Controller
{

    protected $cityService, $stateService,$hospiceService;

    /**
     * constructor for initialize Admin service
     *
     * @param CityService $cityService reference to cityService
     * @param StateService $stateService reference to stateService
     * 
     */
    public function __construct(CityService $cityService, StateService $stateService,HospiceService $hospiceService)
    {
        $this->cityService = $cityService;
        $this->stateService = $stateService;
        $this->hospiceService = $hospiceService;
    }

    /**
     * fetch states from country
     *
     * @param  $countryId
     * @return Response
     */
    public function fetchStates($countryId)
    {
        return $this->stateService->fetchStates($countryId);
    }

    /**
     * fetch cities from state
     *
     * @param  $stateId
     * @return Response
     */
    public function fetchCities($stateId)
    {
        return $this->cityService->fetchCities($stateId);
    }

    public function fetchFacilities($hospiceId)
    {
        return $this->hospiceService->getHospiceFacilitiesList($hospiceId);
    }
     
}
