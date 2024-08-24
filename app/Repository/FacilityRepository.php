<?php

namespace App\Repository;


use App\Models\Facilities;
use App\Models\Hospice;
use App\Models\PasswordReset;
use App\Models\User;
use Mail;
use App\Models\Facility;

class FacilityRepository
{
    /**
     * Send email
     * @return bool
     */
    public function create($data)
    {
        return Facilities::create($data);
    }

    public function fetch($id)
    {
        return Facilities::where('id', $id)->first();
    }
    public function getBranchName($id)
    {
        $data = Facilities::select('name')->where('id',$id)->first();
        return $data->name ?? '';
    }

    public function findAllFacilityList()
    {
        return Facilities::where('status',1)->get()->toArray();
    }

    public function update($data, $id = null)
    {
        return Facilities::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Facilities::where('id', $id)->delete();
    }

    /**
     * Fetch states from the countryId
     * @param array $countryId
     * @return Response
     */
    public function fetchFacility($hospiceId)
    {
        $data = Facility::select('name', 'id')->where('hospice_id', $hospiceId)->where('status',1)->get();
        return response()->json($data);
    }

    public function dropdownData()
    {
        return Facility::select('name', 'id')->where('status',1)->get();
    }
}
