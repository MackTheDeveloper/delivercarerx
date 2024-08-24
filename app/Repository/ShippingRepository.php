<?php

namespace App\Repository;

use App\Models\Shipping;

class ShippingRepository
{
    /**
     * Store shipping information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        return Shipping::create($data);
    }

    /**
     * Update shipping information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {
        return Shipping::where('id', $id)->update($data);
    }

    /**
     * Fetch shipping information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return Shipping::where('id', $id)->first();
    }

    /**
     * delete shipping
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return Shipping::where('id', $id)->delete();
    }
}
