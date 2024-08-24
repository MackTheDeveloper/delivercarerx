<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DeliverImport implements ToCollection
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
                if($row[0] == "first_name" || 
                $row[1] == "last_name" || 
                $row[2] == "name" || 
                $row[3] == "user_logon_name" || 
                $row[4] == "street" ||
                $row[5] == "email" ||
                $row[6] == "city_id"|| 
                $row[7] == "state_id"   ||
                $row[8] == "zipcode"  ||
                $row[9] == "country_id" || 
                $row[10] == "job_title" || 
                $row[11] == "department" || 
                $row[12] == "company" || 
                $row[13] == "manager" ||
                $row[14] == "description" ||  
                $row[15] == "office" ||  
                $row[16] == "phone"  ||
                $row[17] == "initials"
                     )

            {
                continue;
            }
            $data[] = array($row[0],$row[1],$row[2],$row[3],$row[4],
                $row[5],$row[6], $row[7],$row[8], $row[9] , $row[10] , $row[9] 
               , $row[10] , $row[11] ,$row[12] ,$row[13] ,$row[14] ,$row[15] ,$row[16] ,
               $row[17] );
              
        }
        $this->common = $data;
    }

    public function getCommon(): array
    {
        return $this->common;
    }
}
