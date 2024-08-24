<?php

namespace App\Repository;

use App\Models\RefillOrder;
use Auth;

class RefillOrderRepository 
{
    /**
     * Store hospice information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        return RefillOrder::create($data)->id;
    }

    /**
     * Update hospice information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {
        return RefillOrder::where('id', $id)->update($data);
    }

    /**
     * Fetch hospice information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return RefillOrder::where('id', $id)->first();
    }

     /**
     * delete refill orders
     * @param integer $id
     * @return Response
     */
    public function delete($id)
    {
        return RefillOrder::where('id', $id)->delete();
    }

    /**
     * delete refill orders
     * @param integer $id
     * @return Response
     */
    public function deleteLatestOrders($id)
    {
        return RefillOrder::where('id', $id)->delete();
    }
    
}
