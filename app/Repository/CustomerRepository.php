<?php
namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{
    /**
     * Store hospice information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        return customer::create($data);
    }

    /**
     * Update customer information
     * @param array $data
     * @return Response
     */
    public function update($data, $id = null)
    {
        return customer::where('id', $id)->update($data);
    }

    /**
     * Fetch customer information
     * @param $id
     * @return Response
     */
    public function fetch($id)
    {
        return customer::where('id', $id)->first();
    }

    /**
     * Get customer facilities from customerId
     * @param $customerId
     * @return array
     */
    // public function getHospiceFacilities($hospiceId)
    // {
    //     return Facilities::where('hospice_id', $hospiceId)->get()->toArray();
    // }

    /**
     * Create hospice user
     * @param array $data
     * @return Response
     */
    public function createcustomerUser($data)
    {
        return customer::create($data);
    }

    /**
     * Fetch store for users
     * @return array
     */
    public function getDropDownList()
    {
        return customer::select('id', 'name')->whereNull('deleted_at')->where('is_active', 1)->get();
    }
}
