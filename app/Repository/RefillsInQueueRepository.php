<?php

namespace App\Repository;

use App\Models\RefillsInQueue;
use App\Models\RefillOrder;
use Auth;

class RefillsInQueueRepository
{
    public function fetchPatientDetails($id)
    {
        $data = RefillOrder::where('patient_id', $id)->get();
        if($data)
        {
            return $data;
        }
    }
    // /**
    //  * Store hospice information
    //  * @param array $data
    //  * @return Response
    //  */
    // public function create($data)
    // {
    //     return Hospice::create($data);
    // }

    // /**
    //  * Update hospice information
    //  * @param array $data
    //  * @return Response
    //  */
    // public function update($data, $id = null)
    // {
    //     return Hospice::where('id', $id)->update($data);
    // }

    // /**
    //  * Fetch hospice information
    //  * @param $id
    //  * @return Response
    //  */
    // public function fetch($id)
    // {
    //     return Hospice::where('id', $id)->first();
    // }

    // public function findAllHospiceList()
    // {
    //     if (Auth::user()->user_type == 2) {
    //         $hospice = Hospice::where('id', Auth::user()->hospice_id)->get()->toArray();
    //     } else {
    //         $hospice = Hospice::all()->toArray();
    //     }


    //     return $hospice;
    // }

    // /**
    //  * Get hospice facilities from hospiceId
    //  * @param $hospiceId
    //  * @return array
    //  */
    // public function getHospiceFacilities($hospiceId)
    // {
    //     return Facilities::where('hospice_id', $hospiceId)->get()->toArray();
    // }

    // /**
    //  * delete hospice
    //  * @param integer $id
    //  * @return Response
    //  */
    // public function delete($id)
    // {
    //     return Hospice::where('id', $id)->delete();
    // }

    // public function getListNameAndCode()
    // {
    //     return Hospice::select('name', 'code', 'id')->get();
    // }
    // public function getBranchName($id)
    // {
    //     $data = Hospice::select('name')->where('id',$id)->first();
    //     return $data->name ?? '';
    // }
}
