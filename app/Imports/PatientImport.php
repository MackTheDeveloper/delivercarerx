<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PatientImport implements ToCollection
{
    private $common = array();
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $data = [];
        foreach ($collection as $key=>$row) {
            if($key==0){
                if ($row[0] != "FirstName" || $row[1] != "LastName" || $row[2] != "Middle" || $row[3] != "Address 1" || $row[4] != "Address 2" || $row[5] != "City" || $row[6] != "Stat" || $row[7] != "Zip" || $row[8] != "Phone" || $row[9] != "Facility Code" || $row[10] != "Sex"  || $row[11] != "DOB" || $row[12] != "Pat ID" || $row[13] != "Status" || $row[14] != "IPU") {
                    $data[] = 'Column Mismatch';
                    break; 
                }
            }else{
                $data[] = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12], $row[13], $row[14]);
            }
        }

        $this->common = $data;
    }

    public function getCommon(): array
    {
        return $this->common;
    }
}
