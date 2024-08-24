<?php

namespace App\Repository;

use App\Models\State;

class StateRepository
{
    /**
     * Fetch the state
     * @return State list
     */
    public function findAllStateList($countryId)
    {
        $states = State::all()->where('country_id', $countryId)->toArray();
        return $states;
    }

    /**
     * Fetch states from the countryId
     * @param array $countryId
     * @return Response
     */
    public function fetchStates($countryId)
    {
        $data = State::where('country_id', $countryId)->get(["name", "id"]);
        return response()->json($data);
    }

    /**
     * Fetch state Data from the name
     * @param array $countryId
     * @return Response
     */
    public function fetchStateDataFromName($stateName)
    {
        $state = State::where('name', $stateName)->first();
        return $state;
    }

    /**
     * Add new state
     * @param array $stateData
     * @return Response
     */
    public function addNewState($stateData)
    {
        $stateData = State::create($stateData);
        return $stateData;
    }
}
