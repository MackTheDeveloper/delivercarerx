<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
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
            if($row[0] == "Firstname" || $row[1] == "Lastname" || $row[2] == "Email" || $row[3] == "Phone
                " || $row[4] == "Assigned Facilities" || $row[5] == "Admin"|| $row[6] == "SMS")
            


            {
                continue;
            }
            $data[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6]);
        }
        $this->common = $data;
    }
    public function getCommon(): array
    {
        return $this->common;
    }
}
