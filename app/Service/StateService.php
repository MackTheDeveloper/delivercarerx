<?php

namespace App\Service;

use App\Repository\AdminRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;

class StateService
{

    protected $stateRepo;

    /**
     * @param StateRepository $stateRepo reference to stateRepo
     * 
     */
    public function __construct(StateRepository $stateRepo)
    {
        $this->stateRepo = $stateRepo;
    }

    /** 
     * Get state list
     */
    public function getStateList($countryId)
    {
        return $this->stateRepo->findAllStateList($countryId);
    }

    /** 
     * Fetch states from countryId
     * @param $countryId
     * @return Response
     */
    public function fetchStates($countryId)
    {
        return $this->stateRepo->fetchStates($countryId);
    }

    /** 
     * Fetch state Data from state name
     * @param $stateName
     * @return Response
     */
    public function fetchStateDataFromName($stateName)
    {
        return $this->stateRepo->fetchStateDataFromName($stateName);
    }

    /** 
     * Add new state
     * @param $stateName
     * @return Response
     */
    public function addNewState($stateName)
    {
        $stateData['name'] = $stateName;
        $stateData['country_id'] = 231;
        $stateData['is_active'] = 1;
        return $this->stateRepo->addNewState($stateData);
    }
}
