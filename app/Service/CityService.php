<?php

namespace App\Service;

use App\Repository\AdminRepository;
use App\Repository\CityRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;

class CityService
{

    protected $cityRepo;

    /**
     * @param CityRepository $cityRepo reference to cityRepo
     * 
     */
    public function __construct(CityRepository $cityRepo)
    {
        $this->cityRepo = $cityRepo;
    }

    /** 
     * Fetch cities from stateId
     * @param $stateId
     * @return Response
     */
    public function fetchCities($stateId)
    {
        return $this->cityRepo->fetchCities($stateId);
    }

    /** 
     * Get city list
     */
    public function getCityList($stateId)
    {
        return $this->cityRepo->findAllCityList($stateId);
    }

    /** 
     * Fetch city Data from city name
     * @param $stateId
     * @param $cityName
     * @return Response
     */
    public function fetchCityDataFromName($stateId, $cityName)
    {
        $condition = ['state_id' => $stateId, 'name' => $cityName];
        return $this->cityRepo->fetchCityDataFromName($condition);
    }

    /** 
     * Add new city
     * @param array $cityData
     * @return Response
     */
    public function addNewCity($cityData)
    {
        $cityData['is_active'] = 1;
        return $this->cityRepo->addNewcity($cityData);
    }
}
