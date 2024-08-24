<?php

namespace App\Repository;

use App\Models\Cart;
use App\Models\CartMaster;
use Auth;

class CartRepository
{
    /**
     * Store patient information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        try {
            $value = Cart::create($data);
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
        return Cart::where('id', $id)->update($data);

    }
    /**
     * Fetch patient information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return Cart::where('id', $id)->first();
    }


    /**
     * delete patient
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        Cart::where('id', $id)->delete();
        return 'success';
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
        $data = Cart::select($returnValue)->where($fieldName, $fieldValue)->first();
        if ($data) {
            $return['returnValue'] = $data->$returnValue;
        }
        return $return;
    }
}
