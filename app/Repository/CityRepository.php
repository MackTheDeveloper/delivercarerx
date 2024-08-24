<?php

namespace App\Repository;

use App\Models\Cities;
use App\Models\City;

class CityRepository
{
    /**
     * Fetch cities from the stateId
     * @param array $stateId
     * @return Response
     */
    public function fetchCities($stateId)
    {
        $data = Cities::where('state_id', $stateId)->get(["name", "id"]);
        return response()->json($data);
    }

    /**
     * Fetch the cities
     * @return Cities list
     */
    public function findAllCityList($stateId)
    {
        $states = Cities::all()->where('state_id', $stateId)->toArray();
        return $states;
    }

    /**
     * Fetch city Data from the name
     * @param array $condition
     * @return Response
     */
    public function fetchCityDataFromName($condition)
    {
        $city = City::where($condition)->first();
        return $city;
    }

    /**
     * Add new city
     * @param array $cityData
     * @return Response
     */
    public function addNewCity($cityData)
    {
        $cityData = City::create($cityData);
        return $cityData;
    }
}
