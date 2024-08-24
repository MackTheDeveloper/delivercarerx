<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class FacilityImport implements ToCollection
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
                if($row[0] == "hospice_client" || 
                $row[1] == "hospice_group" || 
                $row[2] == "name" || 
                $row[3] == "address_1" || 
                $row[4] == "address_2" || 
                $row[5] == "city_id" ||
                $row[6] == "state_id"|| 
                $row[7] == "zipcode"   ||
                $row[8] == "email" 
           
                     )

            {
                continue;
            }
            $data[] = array($row[0],$row[1],$row[2],$row[3],$row[4],
                $row[5],$row[6], $row[7],$row[8]
               
                );
              
        }
        $this->common = $data;
    }

    public function getCommon(): array
    {
        return $this->common;
    }
}
