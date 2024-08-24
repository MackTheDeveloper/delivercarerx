<?php

namespace App\Imports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BranchImport implements ToCollection
{


private $common = array();
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $data = [];
        // foreach ($collection as $row) {
        //     if ($row[0]) {

        //         if ($row[0] == "name" || $row[1] == "facilitycode" || $row[2] == "address_1" || $row[3] == "address_2" || $row[4] == "state" || $row[5] == "city" || $row[6] == "zipcode" || $row[7] == "phone" || $row[8] == "carrier")  {
        //             continue;
        //         }
        //         $data[] = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8]);
        //     }
        // }

        foreach ($collection as $key=>$row) {
            if($key==0){
                if ($row[0] != "Name" || $row[1] != "FacilityCode" || $row[2] != "Address_1" || $row[3] != "Address_2" || $row[4] != "State" || $row[5] != "City" || $row[6] != "Zipcode" || $row[7] != "Phone" || $row[8] != "Carrier")  {
                    $data[] = 'Column Mismatch';
                    break; 
                }
            }else{
                $data[] = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8]);

            }
        }

        $this->common = $data;
    }

    public function getCommon(): array
    {
        return $this->common;
    }
}
