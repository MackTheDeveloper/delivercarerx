<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class NewLeafOrdersImport implements ToCollection
{
    private $common = array();
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $data = [];
        foreach ($rows as $row)
        {
            if($row[0] == "delivercare_order_number" && $row[1] == "newleaf_order_number" && $row[2] == "patient_name")

            {
                continue;
            }
            $data[] = array($row[0],$row[1],$row[2]);
              
        }
        $this->common = $data;
    }

    public function getCommon(): array
    {
        return $this->common;
    }
}
