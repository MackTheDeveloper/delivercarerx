<?php

namespace App\Repository;

use App\Models\RefillOrderItems;
use Auth;

class RefillOrderItemsRepository
{
    /**
     * Store hospice information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        return RefillOrderItems::create($data);
    }

    /**
     * Update hospice information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {
        return RefillOrderItems::where('id', $id)->update($data);
    }

    /**
     * Fetch hospice information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return RefillOrderItems::where('id', $id)->first();
    }
}
