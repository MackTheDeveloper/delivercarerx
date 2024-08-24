<?php

namespace App\Repository;

use App\Models\Activities;
use Auth;

class ActivityRepository
{
    /**
     * Store activity information
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        $data['performed_by'] = Auth::user()->id;
        return Activities::create($data);
    }
}
