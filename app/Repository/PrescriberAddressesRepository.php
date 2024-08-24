<?php

namespace App\Repository;

use App\Models\Hospice;
use App\Models\PrescriberAddresses;
use App\Models\Prescriber;
use DB;
use Auth;

class PrescriberAddressesRepository
{
    /**
     * Store patient information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        try {
            $value = PrescriberAddresses::create($data);
            return $value;
        } catch (QueryException $e) {
            dd($e);
        }
        
    }

    /**
     * Update patient information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {   
        return DB::table('prescriber_address')->where('id', $id)->update($data);

    }
    /**
     * Fetch patient information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return PrescriberAddresses::where('id', $id)->first();
    }


    /**
     * delete patient
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return PrescriberAddresses::where('id', $id)->delete();
    }

    public function getAttrById($id, $attr)
    {
        $return = "";
        $data = self::select($attr)->where('id', $id)->first();
        if ($data) {
            $return = $data->$attr;
        }
        return $return;
    }

    public function checkRecordExistBySpecificField($fieldName, $fieldValue, $returnValue)
    {
        $return = array(); 
         $data = DB::table('prescriber_address')->select($returnValue)->where($fieldName, $fieldValue)->first();
         //$data = PrescriberAddresses::select($returnValue)->where($fieldName, $fieldValue)->first();
        
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
